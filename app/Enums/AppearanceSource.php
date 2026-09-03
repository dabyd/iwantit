<?php

namespace App\Enums;

enum AppearanceSource: string
{
    case Manual = 'manual';
    case Datision = 'datision';
    case Ocr = 'ocr';
    case Asr = 'asr';
    case ObjectDetection = 'object_detection';
    case LogoDetection = 'logo_detection';
    case Vlm = 'vlm';
    case Llm = 'llm';
    case Other = 'other';
}
