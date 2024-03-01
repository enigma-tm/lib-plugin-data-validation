<?php

declare(strict_types=1);

namespace Paysera\DataValidator\Validator;

use MadeSimple\Arrays\Arr;
use MadeSimple\Arrays\ArrDots;
use Paysera\DataValidator\Validator\Exception\IncorrectValidationRuleStructure;
use Paysera\DataValidator\Validator\Helper\Str;
use Paysera\DataValidator\Validator\Rules\AbstractRule;

abstract class AbstractValidator
{
    const WILD = '*';

    /**
     * @var AbstractRule[]
     */
    protected array $rules;

    protected array $messages;

    protected array $errors;

    protected string $prefix = '';

    public function __construct()
    {
        $this->rules = [];
        $this->messages = ['rules' => [], 'custom' => []];
        $this->errors = [];
    }

    public function setRuleMessage(string $name, string $message): self
    {
        $this->messages['rules'][$name] = $message;

        return $this;
    }

    public function setAttributeMessage(string $name, string $message): self
    {
        $this->messages['custom'][$name] = $message;

        return $this;
    }

    /**
     * @throws IncorrectValidationRuleStructure
     */
    public function addRule(AbstractRule $rule): self
    {
        $this->rules[$rule->getName()] = $rule;

        return $this;
    }

    public function getValue($array, $pattern)
    {
        $imploded = ArrDots::implode($array);
        $pattern  = sprintf('/^%s$/', str_replace(static::WILD, '[0-9]+', $pattern));

        foreach ($imploded as $attribute => $value) {
            if (preg_match($pattern, $attribute) == 0) {
                continue;
            }

            return $value;
        }

        return null;
    }

    public function getValues($array, $pattern): iterable
    {
        foreach (ArrDots::collate($array, $pattern, static::WILD) as $attribute => $value) {
            yield $attribute => $value;
        }
    }

    public function validate($values, array $ruleSet, string $prefix = null) : bool
    {
        // If there are no rules, there is nothing to validate
        if (empty($ruleSet)) {
            return true;
        }
        $currentPrefix = $this->prefix;
        if ($prefix !== null) {
            $this->prefix .= $prefix . '.';
        }

        // For each pattern and its rules
        foreach ($ruleSet as $pattern => $rules) {
            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
            foreach ($rules as $rule) {
                list($rule, $parameters) = array_pad(explode(':', $rule, 2), 2, '');
                $parameters = array_map('trim', explode(',', $parameters));

                if (Arr::exists($this->rules, $rule)) {
                    if (!$this->rules[$rule]->validate($this, $values, $pattern, $parameters)) {
                        // If the rule failed, we stop checking the rest of the rules for this pattern
                        // @todo: do we need to stop on error for each field separately?
                        break;
                    }
                }
            }
        }
        $this->prefix = $currentPrefix;

        return !$this->hasErrors();
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function addError($attribute, $rule, $replacements = []): void
    {
        $replacements = array_merge([
            ':attribute'    => $this->prefix . $attribute,
            '!(\S+)\|(\S+)' => true,
        ], $replacements ?? []);

        $this->errors[] = [
            'attribute'    => $this->prefix . $attribute,
            'rule'         => $rule,
            'replacements' => $replacements,
        ];
    }

    public function getProcessedErrors(): array
    {
        $errors = [];

        foreach ($this->errors as $error) {
            // Process replacements
            $message = ArrDots::get($this->messages['custom'], $error['attribute'])
                ?? ArrDots::get($this->messages['rules'], $error['rule']);
            foreach ($error['replacements'] as $search => $replace) {
                switch ($search[0]) {
                    case ':':
                        $message = str_replace($search, Str::prettifyAttributeName($replace), $message);
                        break;
                    case '!':
                        if (!$replace) {
                            break;
                        }
                        // Check if the attribute is singular (use group 1) or plural (use group 2)
                        // Group 2 if plural, group 1 if singular
                        $replace = substr($error['replacements'][':attribute'] ?? '', -1, 1) !== static::WILD
                            ? '$1' : '$2';
                        $message = preg_replace("/$search/", $replace, $message);
                        break;

                    case '%':
                    default:
                        $message = str_replace($search, $replace, $message);
                        break;
                }
            }
            $errors[$error['attribute']][$error['rule']] = $message;
        }

        return $errors;
    }
}
