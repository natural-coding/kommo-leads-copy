<?php

require_once __DIR__ . '/../src/bootstrap.php';

header('Location: /3rd_party_redirect.php?code=' . $_ENV['AMOCRM_AUTHORIZATION_CODE']);