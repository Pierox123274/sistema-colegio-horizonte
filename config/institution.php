<?php

return [
    'name' => env('INSTITUTION_NAME', 'I.E.P. Horizonte'),
    'identifier' => env('INSTITUTION_IDENTIFIER', 'RUC DEMO 20123456789'),
    'address' => env('INSTITUTION_ADDRESS', 'Av. Demo 123, Lima - Peru'),
    'receipt_message' => env('INSTITUTION_RECEIPT_MESSAGE', 'Gracias por su pago. Conserve este comprobante para cualquier consulta.'),
    'show_qr_demo' => filter_var(env('INSTITUTION_SHOW_QR_DEMO', true), FILTER_VALIDATE_BOOL),
];
