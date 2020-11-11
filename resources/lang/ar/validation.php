<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class Some of these rules have multiple versions such
    | as the size rules Feel free to tweak each of these messages here
    |
    */

    'accepted' => 'The :attribute must be accepted',
    'active_url' => 'The :attribute is not a valid URL',
    'after' => ' :attribute يجب أن يكون بعد :date',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date',
    'alpha' => 'The :attribute may only contain letters',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores',
    'alpha_num' => 'The :attribute may only contain letters and numbers',
    'array' => ' يجب إختيار العديد من :attribute',
    'before' => 'The :attribute must be a date before :date',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date',
    'between' => [
        'numeric' => ':attribute يجب أن تتراوح بين :min و :max',
        'file' => 'The :attribute must be between :min and :max kilobytes',
        'string' => 'The :attribute must be between :min and :max characters',
        'array' => 'The :attribute must have between :min and :max items',
    ],
    'boolean' => 'The :attribute field must be true or false',
    'confirmed' => 'The :attribute confirmation does not match',
    'date' => 'The :attribute is not a valid date',
    'date_equals' => 'The :attribute must be a date equal to :date',
    'date_format' => 'من فضلك أدخل :attribute بصورة صحيحة ',
    'different' => 'The :attribute and :other must be different',
    'digits' => 'The :attribute must be :digits digits',
    'digits_between' => 'The :attribute must be between :min and :max digits',
    'dimensions' => 'The :attribute has invalid image dimensions',
    'distinct' => ' يوجد تكرار في :attribute',
    'email' => 'من فضلك أدخل البريد الالكترونى بصورة صحيحة',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    'exists' => ' :attribute الذي تم إختياره غير موجود',
    'file' => 'The :attribute must be a file',
    'filled' => 'The :attribute field must have a value',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value',
        'file' => 'The :attribute must be greater than :value kilobytes',
        'string' => 'The :attribute must be greater than :value characters',
        'array' => 'The :attribute must have more than :value items',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value',
        'file' => 'The :attribute must be greater than or equal :value kilobytes',
        'string' => 'The :attribute must be greater than or equal :value characters',
        'array' => 'The :attribute must have :value items or more',
    ],
    'image' => ':attribute غير صالحة',
    'in' => ' :attribute بصورة خاطئة',
    'in_array' => 'The :attribute field does not exist in :other',
    'integer' => 'The :attribute must be an integer',
    'ip' => 'The :attribute must be a valid IP address',
    'ipv4' => 'The :attribute must be a valid IPv4 address',
    'ipv6' => 'The :attribute must be a valid IPv6 address',
    'json' => 'The :attribute must be a valid JSON string',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value',
        'file' => 'The :attribute must be less than :value kilobytes',
        'string' => 'The :attribute must be less than :value characters',
        'array' => 'The :attribute must have less than :value items',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value',
        'file' => 'The :attribute must be less than or equal :value kilobytes',
        'string' => 'The :attribute must be less than or equal :value characters',
        'array' => 'The :attribute must not have more than :value items',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max',
        'file' => ':attribute يجب أن تكون أقل من :max كيلو بايت',
        'string' => ' العنصر :attribute  يجب ألا يتخطي ال:max حرف',
        'array' => 'العنصر :attribute يجب أن يكون أقل من :max',
    ],
    'mimes' => ':attribute يجب أن يكون من النوع :values',
    'mimetypes' => 'The :attribute must be a file of type: :values',
    'min' => [
        'numeric' => 'The :attribute must be at least :min',
        'file' => 'The :attribute must be at least :min kilobytes',
        'string' => ' العنصر :attribute  يجب أن يزيد عن :min حرف',
        'array' => 'The :attribute must have at least :min items',
    ],
    'not_in' => 'The selected :attribute is invalid',
    'not_regex' => 'The :attribute format is invalid',
    'numeric' => ' يجب أن يكون :attribute عبارة عن رقم',
    'present' => 'The :attribute field must be present',
    'regex' => 'The :attribute format is invalid',
    'required' => ' من فضلك تحقق من إدخال :attribute ',
    'required_if' => 'The :attribute field is required when :other is :value',
    'required_unless' => 'The :attribute field is required unless :other is in :values',
    'required_with' => 'The :attribute field is required when :values is present',
    'required_with_all' => 'The :attribute field is required when :values are present',
    'required_without' => 'The :attribute field is required when :values is not present',
    'required_without_all' => 'The :attribute field is required when none of :values are present',
    'same' => ' يجب أن يكون :attribute و :other متطابقين',
    'size' => [
        'numeric' => 'The :attribute must be :size',
        'file' => 'The :attribute must be :size kilobytes',
        'string' => 'The :attribute must be :size characters',
        'array' => 'The :attribute must contain :size items',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => ' :attribute يجب أن يكون حروفاً فقط ',
    'timezone' => 'The :attribute must be a valid zone',
    'unique' => ' لقد تم التسجيل بهذا :attribute مسبقاً من فضلك قم بإدخال واحداً اخر',
    'uploaded' => 'The :attribute failed to upload',
    'url' => 'The :attribute format is invalid',
    'uuid' => 'The :attribute must be a valid UUID',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attributerule" to name the lines This makes it quick to
    | specify a specific custom language line for a given attribute rule
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email" This simply helps us make our message more expressive
    |
    */

    'attributes' => [
        'id.*'=>'الكود المرجعى',
        'id'=>'الكود المرجعى',
        'teacher_id'=>'المدرس',
        'appointment_id'=>'الموعد',
        'exam_id'=>'كود الامتحان',
        'package_id' => 'رقم الباكدج',
        'first_name'=>'الاسم الاول',
        'last_name'=>'الاسم الاخير',
        'email'=>'البريد الالكترونى',
        'password'=>'الرقم السرى',
        'old_password' => 'الرقم السرى القديم',
        'new_password' => 'الرقم السرى الجديد',
        'verify_password'=>'تأكيد الرقم السرى',
        'mobile'=>'رقم الهاتف',
        'parent_mobile1'=>'هاتف ولي الامر 1',
        'parent_mobile2'=>'هاتف ولي الامر 2',
        'days' => 'الايام',
        'time_from'=>'الوقت من',
        'time_to'=>'الوقت إلى',
        'year'=>'السنة الدراسية',
        'exam_name'=>'اسم الامتحان',
        'duration'=>'مدة الامتحان',
        'main_question'=>'القطعة',
        'question'=>'السؤال',
        'true_respond'=>'الاجابة الصحيحة',
        'degree'=>'الدرجة',
        'responds'=>'الاجابات',
        'responds.*'=>'الاختيارات',
        'outside_counter'=>'الترقيم الرئيسى',
        'inside_counter'=>'الترقيم الفرعي',
        'desc'=>'الوصف',
        'question_id'=>'كود السؤال',
        'request_id' => 'رقم الطلب',
        'solves' => 'الحلول',
        'solves.*.question_id' => 'كود السؤال',
        'solves.*.respond' => 'الإجابات',
        'solves.*.images' => 'الصور',
        'solves.*.images.*' => 'الصور',
        'students' => 'الطلاب',
        'students.*' => 'بيانات الطلاب',
        'type' => 'النوع',
        'marks' => 'التصحيح',
        'marks.*.id' => 'الكود المرجعى',
        'marks.*.degree' => 'درجات التصحيح',
        'marks.*.question_id' => 'كود السؤال',
        'request_ids' => 'الطلاب',
        'request_ids.*' => 'الطلاب',
        'message' => 'الرسالة',
        'year_id' => 'السنة الدراسية',
        'group_id' => 'المجموعة',

    ],

];
