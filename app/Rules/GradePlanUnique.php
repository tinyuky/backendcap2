<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Grades_Plans;

class GradePlanUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $list = Grades_Plans::all();
        $check = [];
        foreach ($list as $row) {
            $check[] = $row->name.$row->hk;
        }
        if(in_array($value,$check)){
            return false;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Kế hoạch đã tồn tại';
    }
}
