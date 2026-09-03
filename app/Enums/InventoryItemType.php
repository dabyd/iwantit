<?php

namespace App\Enums;

enum InventoryItemType: string
{
    case Product = 'product';
    case Brand = 'brand';
    case Person = 'person';
    case Character = 'character';
    case Location = 'location';
    case Object = 'object';
    case Identifier = 'identifier';
    case Artwork = 'artwork';
    case Screen = 'screen';
    case Document = 'document';
    case Text = 'text';
    case AudioWork = 'audio_work';
}
