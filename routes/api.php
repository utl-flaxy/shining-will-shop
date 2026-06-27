<?php

use Illuminate\Support\Facades\Route;

/**
 * Stripe webhook (stateless, CSRFなし)
 * VerifyCsrfToken:: は設定済みだが、API側はそもそもCSRFを通らない
 */

