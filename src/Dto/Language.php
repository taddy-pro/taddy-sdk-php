<?php

namespace Taddy\Sdk\Dto;

enum Language: string {
    case Abkhazian = 'ab';
    case Afar = 'aa';
    case Afrikaans = 'af';
    case Akan = 'ak';
    case Albanian = 'sq';
    case Amharic = 'am';
    case Arabic = 'ar';
    case Aragonese = 'an';
    case Armenian = 'hy';
    case Assamese = 'as';
    case Avaric = 'av';
    case Avestan = 'ae';
    case Aymara = 'ay';
    case Azerbaijani = 'az';
    case Bambara = 'bm';
    case Bashkir = 'ba';
    case Basque = 'eu';
    case Belarusian = 'be';
    case Bengali = 'bn';
    case Bislama = 'bi';
    case Bosnian = 'bs';
    case Breton = 'br';
    case Bulgarian = 'bg';
    case Burmese = 'my';
    case Catalan = 'ca';
    case Chamorro = 'ch';
    case Chechen = 'ce';
    case Chichewa = 'ny';
    case Chinese = 'zh';
    case ChurchSlavonic = 'cu';
    case Chuvash = 'cv';
    case Cornish = 'kw';
    case Corsican = 'co';
    case Cree = 'cr';
    case Croatian = 'hr';
    case Czech = 'cs';
    case Danish = 'da';
    case Divehi = 'dv';
    case Dutch = 'nl';
    case Dzongkha = 'dz';
    case English = 'en';
    case Esperanto = 'eo';
    case Estonian = 'et';
    case Ewe = 'ee';
    case Faroese = 'fo';
    case Fijian = 'fj';
    case Finnish = 'fi';
    case French = 'fr';
    case WesternFrisian = 'fy';
    case Fulah = 'ff';
    case Gaelic = 'gd';
    case Galician = 'gl';
    case Ganda = 'lg';
    case Georgian = 'ka';
    case German = 'de';
    case Greek = 'el';
    case Kalaallisut = 'kl';
    case Guarani = 'gn';
    case Gujarati = 'gu';
    case Haitian = 'ht';
    case Hausa = 'ha';
    case Hebrew = 'he';
    case Herero = 'hz';
    case Hindi = 'hi';
    case HiriMotu = 'ho';
    case Hungarian = 'hu';
    case Icelandic = 'is';
    case Ido = 'io';
    case Igbo = 'ig';
    case Indonesian = 'id';
    case Interlingua = 'ia';
    case Interlingue = 'ie';
    case Inuktitut = 'iu';
    case Inupiaq = 'ik';
    case Irish = 'ga';
    case Italian = 'it';
    case Japanese = 'ja';
    case Javanese = 'jv';
    case Kannada = 'kn';
    case Kanuri = 'kr';
    case Kashmiri = 'ks';
    case Kazakh = 'kk';
    case CentralKhmer = 'km';
    case Kikuyu = 'ki';
    case Kinyarwanda = 'rw';
    case Kirghiz = 'ky';
    case Komi = 'kv';
    case Kongo = 'kg';
    case Korean = 'ko';
    case Kuanyama = 'kj';
    case Kurdish = 'ku';
    case Lao = 'lo';
    case Latin = 'la';
    case Latvian = 'lv';
    case Limburgan = 'li';
    case Lingala = 'ln';
    case Lithuanian = 'lt';
    case LubaKatanga = 'lu';
    case Luxembourgish = 'lb';
    case Macedonian = 'mk';
    case Malagasy = 'mg';
    case Malay = 'ms';
    case Malayalam = 'ml';
    case Maltese = 'mt';
    case Manx = 'gv';
    case Maori = 'mi';
    case Marathi = 'mr';
    case Marshallese = 'mh';
    case Mongolian = 'mn';
    case Nauru = 'na';
    case Navajo = 'nv';
    case NorthNdebele = 'nd';
    case SouthNdebele = 'nr';
    case Ndonga = 'ng';
    case Nepali = 'ne';
    case Norwegian = 'no';
    case NorwegianBokmal = 'nb';
    case NorwegianNynorsk = 'nn';
    case SichuanYi = 'ii';
    case Occitan = 'oc';
    case Ojibwa = 'oj';
    case Oriya = 'or';
    case Oromo = 'om';
    case Ossetian = 'os';
    case Pali = 'pi';
    case Pashto = 'ps';
    case Persian = 'fa';
    case Polish = 'pl';
    case Portuguese = 'pt';
    case Punjabi = 'pa';
    case Quechua = 'qu';
    case Romanian = 'ro';
    case Romansh = 'rm';
    case Rundi = 'rn';
    case Russian = 'ru';
    case NorthernSami = 'se';
    case Samoan = 'sm';
    case Sango = 'sg';
    case Sanskrit = 'sa';
    case Sardinian = 'sc';
    case Serbian = 'sr';
    case Shona = 'sn';
    case Sindhi = 'sd';
    case Sinhala = 'si';
    case Slovak = 'sk';
    case Slovenian = 'sl';
    case Somali = 'so';
    case SouthernSotho = 'st';
    case Spanish = 'es';
    case Sundanese = 'su';
    case Swahili = 'sw';
    case Swati = 'ss';
    case Swedish = 'sv';
    case Tagalog = 'tl';
    case Tahitian = 'ty';
    case Tajik = 'tg';
    case Tamil = 'ta';
    case Tatar = 'tt';
    case Telugu = 'te';
    case Thai = 'th';
    case Tibetan = 'bo';
    case Tigrinya = 'ti';
    case Tonga = 'to';
    case Tsonga = 'ts';
    case Tswana = 'tn';
    case Turkish = 'tr';
    case Turkmen = 'tk';
    case Twi = 'tw';
    case Uighur = 'ug';
    case Ukrainian = 'uk';
    case Urdu = 'ur';
    case Uzbek = 'uz';
    case Venda = 've';
    case Vietnamese = 'vi';
    case Volapuk = 'vo';
    case Walloon = 'wa';
    case Welsh = 'cy';
    case Wolof = 'wo';
    case Xhosa = 'xh';
    case Yiddish = 'yi';
    case Yoruba = 'yo';
    case Zhuang = 'za';
    case Zulu = 'zu';
}
