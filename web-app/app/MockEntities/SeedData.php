<?php

namespace App\MockEntities;

class SeedData
{

public static $assignment_statuses = ['Applied',
                                        'Selected',
                                        'Invited',
                                        'Accepted',
                                        'Contract',
                                        'Confirmed',
                                        'Declined',
                                        'Withdrew'];


public static $contract_statuses = [
                                    'Not Created',
                                    'Contract Sent',
                                    'Contract Signed'
                                    ];

public static $payment_types     = [
                                    'Cheque',
                                    'Electronic Transfer'
                                    ];


public static $regions           = [

                                    [      "id"=> "BC",
                                    "name"=> "British Columbia"
                                    ],
                                    [
                                    "id"=> "YT",
                                    "name"=> "Yukon Territory"
                                    ],
                                    [
                                    "id"=> "AB",
                                    "name"=> "Alberta"
                                    ],
                                    [
                                    "id"=> "SK",
                                    "name"=> "Saskatchewan"
                                    ],
                                    [
                                    "id"=> "MB",
                                    "name"=> "Manitoba"
                                    ],
                                    [
                                    "id"=> "ON",
                                    "name"=> "Ontario"
                                    ],
                                    [
                                    "id"=> "QC",
                                    "name"=> "Quebec"
                                    ],
                                    [
                                    "id"=> "NB",
                                    "name"=> "New Brunswick"
                                    ],
                                    [
                                    "id"=> "PE",
                                    "name"=> "Prince Edward Island"
                                    ],
                                    [
                                    "id"=> "NS",
                                    "name"=> "Nova Scotia"
                                    ],
                                    [
                                    "id"=> "NF",
                                    "name"=> "Newfoundland"
                                    ],
                                    [
                                    "id"=> "NW",
                                    "name"=> "Northwest Territory"
                                    ],
                                    [
                                    "id"=> "NU",
                                    "name"=> "Nunavut"
                                    ],
                                    [
                                    "id"=> "WA",
                                    "name"=> "Washington"
                                    ]

                                    ];

public static $roles     = [
                            [
                            "name"=> "Team Lead",
                            "rate"=> 340
                            ],
                            [
                            "name"=> "Exemplar",
                            "rate"=> 340
                            ],
                            [
                            "name"=> "Marker",
                            "rate"=> 300
                            ],
                            [
                            "name"=> "Chair",
                            "rate"=> 350
                            ],
                            [
                            "name"=> "Vice-Chair",
                            "rate"=> 340
                            ],
                            [
                            "name"=> "Section Head",
                            "rate"=> 325
                            ],
                            [
                            "name"=> "Team Member",
                            "rate"=> 300
                            ],
                            ];



public static $session_activities      = [
                            [
                            "code"=> "INA",
                            "name"=> "Inactive Session Type"
                            ],
                            [
                            "code"=> "MAR",
                            "name"=> "Marking"
                            ],
                            [
                            "code"=> "EXE",
                            "name"=> "Exemplars"
                            ],
                            [
                            "code"=> "STD",
                            "name"=> "Standard Setting"
                            ],
                            [
                            "code"=> "MON",
                            "name"=> "Monitoring"
                            ],
                            [
                            "code"=> "REV",
                            "name"=> "Review"
                            ],
                            [
                            "code"=> "DEV",
                            "name"=> "Development"
                            ],
                            [
                            "code"=> "CRD",
                            "name"=> "Credentialing-Facilitation"
                            ],
                            [
                            "code"=> "PRP",
                            "name"=> "Prep marking site "
                            ],
                            [
                            "code"=> "WRT",
                            "name"=> "Writing – marking manuals, scoring guides, credentialing materials"
                            ],
                            [
                            "code"=> "MEET",
                            "name"=> "Meeting"
                            ],
                            [
                            "code"=> "TRAN",
                            "name"=> "Translation"
                            ],
                            [
                            "code"=> "CAR",
                            "name"=> "Classroom assessment and marking"
                            ]
                            ];


public static $session_types         = [
                                [
                                "code"=> "INAC",
                                "name"=> "Inactive Type Name"
                                ],
                                [
                                "code"=> "FSA 4 RE",
                                "name"=> "Foundation Skills Assessment Reading English, Grade 4"
                                ],
                                [
                                "code"=> "FSA 7 RE",
                                "name"=> "Foundation Skills Assessment Reading English, Grade 7"
                                ],
                                [
                                "code"=> "FSA 4 RF",
                                "name"=> "Foundation Skills Assessment Reading French, Grade 4"
                                ],
                                [
                                "code"=> "FSA 7 RF",
                                "name"=> "Foundation Skills Assessment Reading French, Grade 7"
                                ],
                                [
                                "code"=> "FSA 4 NU",
                                "name"=> "Foundation Skills Assessment Numeracy, Grade 4"
                                ],
                                [
                                "code"=> "FSA 7 NU",
                                "name"=> "Foundation Skills Assessment Numeracy, Grade 7"
                                ],
                                [
                                "code"=> "GNA 10 E",
                                "name"=> "Graduation Numeracy Assessment English, 10"
                                ],
                                [
                                "code"=> "GNA 10 F",
                                "name"=> "Graduation Numeracy Assessment French, 10"
                                ],
                                [
                                "code"=> "GLA 10 E",
                                "name"=> "Graduation Literacy Assessment English, 10 "
                                ],
                                [
                                "code"=> "GLA 10 P",
                                "name"=> "Graduation Literacy Assessment Première, 10"
                                ],
                                [
                                "code"=> "GLA 12 E",
                                "name"=> "Graduation Literacy Assessment English, 12"
                                ],
                                [
                                "code"=> "GLA 12 P",
                                "name"=> "Graduation Literacy Assessment Première, 12"
                                ],
                                [
                                "code"=> "GLA 12 I",
                                "name"=> "Graduation Literacy Assessment Immersion, 12"
                                ]
                                ];

}



