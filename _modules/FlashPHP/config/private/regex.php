<?php

use FlashPHP\helpers\OldConfig;

OldConfig::default('REGEX_ALL_LETTERS', '-\'a-zA-ZÀ-ÖØ-öø-ÿ');
OldConfig::default('REGEX_BASIC_LETTERS', 'a-zA-Z');
OldConfig::default('REGEX_EMAIL', "");
OldConfig::default('REGEX_PASSWORD', "");