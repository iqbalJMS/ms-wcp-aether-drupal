<?php

namespace Drupal\brimw\Requests;


use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Drupal;

class KursRequest extends BaseRequest
{
    private $availableTypes = [
        'calcBuyCounter',
        'calcBuyeRate',
        'calcSellCounter',
        'calcSelleRate',
    ];
      
    public function validateType(string $type)
    {
        return in_array($type, $this->availableTypes);
    }

    public function validate(): void
    {
        $availableCurrencies = array_merge(array_column(\Drupal::service('brimw.kurs_remote_data')->getKurs(), 'currency'), ['IDR']);

        $type = $this->request->get('type');
        if (!$this->validateType($type)) {
            $this->finalizeValidation(['type' => 'Invalid type']);
        }

        $this->finalizeValidation(array_merge(
            $this->rules(
                'amount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ),
            $this->rules(
                'fromCurrency',
                new NotBlank,
            ),
            $this->rules(
                'toCurrency',
                new NotBlank, 
            )
        ));
    }
}