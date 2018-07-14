<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Courses;

class CourseUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $list = Courses::all();
        $check = [];
        foreach ($list as $row) {
            $check[] = $row->code.$row->name.$row->hk.$row->grade_id;
        }
        if(in_array($value,$check)){
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Môn học đã tồn tại';
    }
}
