<?php

namespace Drupal\brimw\Requests;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class SearchRequest extends BaseRequest
{
    public function validate(): void
    {
        $this->finalizeValidation(array_merge(
            $this->rules(
                'filter',
                new NotBlank, 
            ),
        ));
    }
}