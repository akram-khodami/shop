<?php

namespace App\Enums;

enum SmsStatus: string
{
  case PENDING = 'pending';
  case RETRY = 'retry';
  case Cancelled = 'cancelled ';
  case SUCCESS = 'success';
  case FAILED = 'failed';
}
