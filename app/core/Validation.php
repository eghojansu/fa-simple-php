<?php

namespace app\core;

class Validation
{
    protected $rules;
    protected $data;
    protected $labels;
    protected $skips;
    protected $error;
    protected $messages = [];

    /**
     * @param array $data  data to validate
     * @param array $rules
     *        $rules = [
     *            'data' => 'required',
     *            // same field can use - (hypen) as prefix (this is tricky way)
     *            '-data' => 'equal(otherfieldvalue)',
     *            // callback can be used too, should return boolean
     *            // @see validate
     *            '--data' => function($value,$field,Validation $app){}
     *        ]
     * @param array $skips
     */
    public function __construct(array $data = [], array $rules = [], array $labels = [], array $skips = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->labels = $labels;
        $this->skips = $skips;
        $this->messages = [
            'required' => '{field} tidak boleh kosong',
            'match' => '{field} tidak valid',
            'minLength' => '{field} minimal {param} karakter',
            'maxLength' => '{field} maksimal {param} karakter',
            'min' => '{field} minimal {param}',
            'max' => '{field} maksimal {param}',
            'equal' => '{field} tidak sama dengan {param}',
            'unique' => '{field} "{value}" sudah ada',
            'exists' => '{field} "{value}" tidak ada',
        ];
    }

    /**
     * Add message
     *
     * @param string $name
     * @param string $message
     */
    public function setMessage($name, $message)
    {
        $this->messages[$name] = $message;

        return $this;
    }

    /**
     * Set data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set rules
     *
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Set labels
     *
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Set skips
     *
     * @param array $skips
     */
    public function setSkips(array $skips)
    {
        $this->skips = $skips;

        return $this;
    }

    /**
     * Check exists
     * Usage:
     *     field => exists(table,[column,[allowEmpty]])
     *
     * @param  mixed $value
     * @param  string $param
     * @param  string $field
     * @return bool
     */
    public function _exists($value, $param, $field)
    {
        $allowEmpty = $this->allowEmpty($param, $value);
        $params = explode(',', $param);
        $table = $params[0];
        $column = empty($params[1]) ? $field : $params[1];

        $db = App::instance()->service(Database::class);
        $filter = [$column.' = ?', $value];
        $data = $db->findOne($table, $filter);

        $passed = !empty($data) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Check unique
     * Usage:
     *     field => unique(table,[id,idValue,[allowEmpty]])
     *
     * @param  mixed $value
     * @param  string $param
     * @param  string $field
     * @return bool
     */
    public function _unique($value, $param, $field)
    {
        $allowEmpty = $this->allowEmpty($param, $value);
        $params = explode(',', $param);
        $table = $params[0];
        $id    = empty($params[1]) ? null : $params[1];
        $idVal = empty($params[2]) ? null : $params[2];

        $db = App::instance()->service(Database::class);
        $filter = [$field.' = ?', $value];
        if ($id) {
            $filter[0] .= ' and '.$id.' <> ?';
            $filter[] = $idVal;
        }
        $data = $db->findOne($table, $filter);

        $passed = empty($data) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Field should equal
     * Usage:
     *     field => equal(fieldValue,[allowEmpty])
     * @param  string|int $value
     * @param  string $param
     * @return bool
     */
    public function _equal($value, $param)
    {
        $allowEmpty = $this->allowEmpty($param, $value);
        $passed = ($value === $param) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Field is required
     * Usage:
     *     field => required()
     * @param  string $value
     * @return bool
     */
    public function _required($value)
    {
        $passed = $value !== '';

        return (bool) $passed;
    }

    /**
     * Field should match
     * Usage:
     *     field => match(pattern,[allowEmpty])
     * Remember parenthesis sign will cause error
     * @param  string $value
     * @param  string $param
     * @return bool
     */
    public function _match($value, $param)
    {
        $allowEmpty = $this->allowEmpty($param, $value);
        $checkPattern = '/'.$param.'/i';
        $passed = preg_match($checkPattern, $value) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Min length
     * Usage:
     *     field => minLength(3,[allowEmpty])
     * @param  string $value
     * @param  int $length
     * @return bool
     */
    public function _minLength($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = (strlen($value) >= $length) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Max length
     * Usage:
     *     field => maxLength(3,[allowEmpty])
     * @param  string $value
     * @param  int $length
     * @return bool
     */
    public function _maxLength($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = (strlen($value) <= $length) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Min
     * Usage:
     *     field => min(3,[allowEmpty])
     * @param  string $value
     * @param  int $length
     * @return bool
     */
    public function _min($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = ($value >= $length) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Max
     * Usage:
     *     field => max(3,[allowEmpty])
     * @param  string $value
     * @param  int $length
     * @return bool
     */
    public function _max($value, $length)
    {
        $allowEmpty = $this->allowEmpty($length, $value);
        $passed = ($value <= $length) || $allowEmpty;

        return (bool) $passed;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get rules
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get skipped field
     * @return array
     */
    public function getSkips()
    {
        return $this->skips;
    }

    /**
     * Do validatiion
     */
    public function validate()
    {
        foreach ($this->rules as $fields => $rule) {
            $fields = array_filter(explode(',', str_replace(' ', '', ltrim($fields, '-'))));
            $rule = $this->extractRule($rule);
            $message = empty($rule['message'])?null:$rule['message'];
            $param = empty($rule['param'])?null:$rule['param'];
            $ruleToCheck = is_string($rule['rule'])?'_'.$rule['rule']:null;

            if ($ruleToCheck && !method_exists($this, $ruleToCheck)) {
                throw new Exception('No validation for '.$rule['rule'], 1);
            }

            foreach ($fields as $field) {
                if (!isset($this->data[$field]) || in_array($field, $this->skips)) {
                    continue;
                }

                $value = $this->data[$field];
                if ($ruleToCheck) {
                    $params = [$value,$param,$field];
                    $result = call_user_func_array([$this, $ruleToCheck], $params);
                    if (!$result) {
                        $this->setError($field, $value, $message, $param, $rule['rule']);
                        break(2);
                    }
                } else {
                    $params = [$value,$field,$this];
                    call_user_func_array($rule['rule'], $params);
                }
            }
        }

        return $this;
    }

    /**
     * Set error
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @param string $params
     * @param string $rule
     */
    public function setError($field, $value, $message, $params = null, $rule = null)
    {
        $params = preg_replace('/,\s*allowEmpty$/', '', $params);
        $label = isset($this->labels[$field])?$this->labels[$field]:
            ucwords(str_replace('_', ' ', $field));
        $replace = [
            '{field}' => $label,
            '{value}' => $value,
            '{param}' => $params,
            '{rule}' => $rule,
            ];
        if ('match' !== $rule) {
            foreach (explode(',', $params) as $key => $param) {
                $replace['{param_'.$key.'}'] = $param;
            }
        }
        $message = str_replace(
            array_keys($replace),
            array_values($replace),
            $message?:$this->messages[$rule]);
        $this->error = $message;

        return $this;
    }

    /**
     * Is valid
     * @return boolean
     */
    public function valid()
    {
        return (bool) empty($this->error);
    }

    /**
     * Is invalid
     * @return boolean
     */
    public function invalid()
    {
        return !$this->valid();
    }

    /**
     * Has error
     * @return boolean
     */
    public function hasError()
    {
        return !$this->valid();
    }

    /**
     * Get error
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    protected function extractRule($rule)
    {
        if (is_callable($rule)) {
            return ['rule'=>$rule];
        }

        $pattern = '/^(?<rule>\w+)(?:\((?<param>[^\)]+)\))?(?:,(?<message>.+))?/';
        preg_match($pattern, $rule, $match);

        return array_filter($match, function($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function allowEmpty(&$param, $value)
    {
        $new_param = preg_replace('/,\s*allowEmpty$/', '', $param);
        $result = (($param !== $new_param) && '' === $value);
        $param = $new_param;

        return $result;
    }
}
