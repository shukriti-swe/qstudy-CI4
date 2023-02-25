<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation extends BaseConfig
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

     //User profile  update---------
     public $schoolValidate = [
        'school_name' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The school name field is required',
            ],
        ],
       'email' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The email name field is required',
            ],
        ],
        'password' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The password name field is required',
            ],
        ],
        'cnfpassword' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The cnfpassword name field is required',
            ],
        ],
        'phone' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The phone name field is required',
            ],
        ],

    ]; 

    public $resetValidate = [
        'user_pass' => [
            'label'  => 'user_pass',
            'rules'  => 'required',
            'errors' => [
                'required' => 'Password Is Required',
            ],
        ],
        're_pass' => [
            'label'  => 're_pass',
            'rules'  => 'required',
            'errors' => [
                'required' => 'Confirm Password Is Required',
            ],
        ],
    ];

    public $productValidate = [
        'product_title' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The product title field is required',
            ],
        ],
       'product_details' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The product details field is required',
            ],
        ],
        'product_point' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The product point field is required',
            ],
        ],
        // 'image' => [
        //     'rules'  => 'ext_in[image,gif|jpg|png]|max_size[image,1024]|max_width[image,1024]|max_height[image,768]',
        // ],
        
    ];

    public $addDialogueValidate = [
        'dialogue_body' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The dialogue body field is required',
            ],
        ],
    ];  
    
    public $vedioUploadValidate = [
        'serial_num' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The serial num field is required',
            ],
        ],
    ]; 

    public $edit_templeteValidate = [
        'setting_key' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The setting key field is required',
            ],
        ],
        'setting_value' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The setting value field is required',
            ],
        ]
    ]; 

    public $sms_templetes_templeteValidate = [
        'setting_value' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The setting value field is required',
            ],
        ],
    ]; 

    public $store_groupboardValidate = [
        'room_id' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The room id field is required',
            ],
        ],
    ];

    public $answer_machingValidate = [
        'answer' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The answer field is required',
            ],
        ],
    ];

    public $save_question_store_data = [
        'country' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The country field is required',
            ],
        ],
        'grade' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The grade field is required',
            ],
        ],
        'subject' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The subject field is required',
            ],
        ],
        'chapter' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The chapter field is required',
            ],
        ],
        'tutor_title' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The tutor_title field is required',
            ],
        ],
        'student_title' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The student_title field is required',
            ],
        ],
        'pdf_order' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The pdf_order field is required',
            ],
        ],
        'student_title' => [
            'rules'  => 'required',
             'errors' => [
                'required' => 'The student_title field is required',
            ],
        ]
    ];
    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------
}
