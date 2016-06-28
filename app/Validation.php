<?php

class Validation
{
    protected $rules;
    protected $data;
    protected $skips;
    protected $error;
    protected $messages = [];

    public function __construct(array $data, array $rules, array $skips = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->skips = $skips;
        $this->messages = [
            'required' => '{field} tidak boleh kosong',
            'match' => '{field} tidak valid',
            'minLength' => '{field} minimal {param} karakter',
            'maxLength' => '{field} maksimal {param} karakter',
            'min' => '{field} minimal {param}',
            'max' => '{field} maksimal {param}',
            'equal' => '{field} tidak sama dengan {param}',
        ];
    }

    public function _equal($value, $param)
    {
        $allowEmpty = $this->allowEmpty($param, $value);
        $passed = ($value === $param) || $allowEmpty;

        return (bool) $passed;
    }

    public function _required($value)
    {
        $passed = $value !== '';

        return (bool) $passed;
    }

    public function _match($value, $pattern)
    {
        $checkPattern = '/'.$pattern.'/i';
        $passed = preg_match($checkPattern, $value);

        return (bool) $passed;
    }

    public function _minLength($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = (strlen($value) >= $length) || $allowEmpty;

        return (bool) $passed;
    }

    public function _maxLength($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = (strlen($value) <= $length) || $allowEmpty;

        return (bool) $passed;
    }

    public function _min($value, $length)
    {
        $passed = $value >= $length;

        return (bool) $passed;
    }

    public function _max($value, $length)
    {
        $passed = $value <= $length;

        return (bool) $passed;
    }

    public function validate()
    {
        foreach ($this->rules as $fields => $rule) {
            $fields = array_filter(explode(',', str_replace(' ', '', $fields)));
            $rule = $this->extractRule($rule);
            $message = empty($rule['message'])?null:$rule['message'];
            $param = empty($rule['param'])?null:$rule['param'];
            $ruleToCheck = '_'.$rule['rule'];

            if (!method_exists($this, $ruleToCheck)) {
                throw new Exception('No validation for '.$ruleToCheck, 1);
            }

            foreach ($fields as $field) {
                if (!isset($this->data[$field]) || in_array($field, $this->skips)) {
                    continue;
                }

                $value = $this->data[$field];
                $params = [$value,$param,$field];
                $result = call_user_func_array([$this, $ruleToCheck], $params);
                if (!$result) {
                    $this->setError($field, $value, $message, $param, $rule['rule']);
                    break(2);
                }
            }
        }

        return $this;
    }

    public function setError($field, $value, $message, $params, $rule)
    {
        $params = preg_replace('/(,allowEmpty)/', '', $params);
        $message = str_replace(['{field}','{value}','{param}'], [
            ucwords(preg_replace('/_(\w)/', ' \\1', $field)),
            $value,
            $params
            ], $message?:$this->messages[$rule]);
        $this->error = $message;

        return $this;
    }

    public function valid()
    {
        return (bool) empty($this->error);
    }

    public function hasError()
    {
        return !$this->valid();
    }

    public function getError()
    {
        return $this->error;
    }

    protected function extractRule($rule)
    {
        $pattern = '/^(?<rule>\w+)(?:\((?<param>[^\)]+)\))?(?:,(?<message>.+))?/';
        preg_match($pattern, $rule, $match);

        return array_filter($match, function($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function allowEmpty(&$param, $value)
    {
        $xtmp = explode(',', $param);
        $param = $xtmp[0];

        if (isset($xtmp[1]) && 'allowEmpty'===$xtmp[1] && ''===$value) {
            return true;
        }

        return false;
    }
}