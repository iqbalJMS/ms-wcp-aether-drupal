<?php

namespace Drupal\brimw\Requests;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class SimulationRequest extends BaseRequest
{
    private $availableTypes = [
        'estimateKpr',
        'estimateKprs',
        // 'estimateBritamaRencana',
        // 'estimateBriguna',
        'estimateBrigunaKarya',
        'estimateBrigunaPurna',
        'estimateDeposito',
        'estimateDepositoValas',
        'estimateDepositoBusiness',
        // 'estimateInvestmentDPLK',
        'estimateInvestment',
    ];
      
    public function validateType(string $type)
    {
        return in_array($type, $this->availableTypes);
    }

    public function validate(): void
    {
        $type = $this->request->get('type');
        if (!$this->validateType($type)) {
            $this->finalizeValidation(['type' => 'Invalid type']);
        }

        $validateMethod = 'validate'.ucfirst($type);
        if (method_exists($this, $validateMethod)){
            $this->{$validateMethod}();
        }
    }

    protected function validateEstimateKpr(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'installmentAmount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ),
            $this->rules(
                'installmentTerm',
                new NotBlank, 
                new Range(min: 1, max: 20),
            )
        ));
    }

    protected function validateEstimateKprs(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'installmentAmount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ), 
            $this->rules(
                'installmentTerm',
                new NotBlank, 
                new Range(min: 1, max: 15),
            )
        ));
    }
    
    protected function validateEstimateBrigunaKarya(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'salary',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ), 
            $this->rules(
                'installmentTerm',
                new NotBlank, 
                new Range(min: 1, max: 15),
            ), 
            $this->rules(
                'interestRate',
                new NotBlank, 
                new Range(min: 0.01 / 100, max: 25 / 100),
            ),
        ));

    }
    
    protected function validateEstimateBrigunaPurna()
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'salary',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ),
            $this->rules(
                'installmentTerm',
                new NotBlank, 
                new Range(min: 1, max: 15),
            ),
            $this->rules(
                'interestRate',
                new NotBlank, 
                new Range(min: 0.01 / 100, max: 25 / 100),
            ),
        ));
    }
    
    protected function validateEstimateDeposito()
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
                new Choice([
                    "1", "3", "6", 
                    "12", "24", "36",
                ]),
            ),
        ));
    }
    
    protected function validateEstimateDepositoValas(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
                new Choice([
                    "1", "3", "6", 
                    "12", "24", "36",
                ]),
            ),
        ));
    }
    
    protected function validateEstimateDepositoBusiness(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
                new Range(min: 1, max: 10 * 1000 * 1000 * 1000),
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
                new Choice([
                    "1", "3", "6", 
                    "12", "24", "36",
                ]),
            ),
        ));
    }
    
    protected function validateEstimateInvestment(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'investmentAmount',
                new NotBlank, 
                new Range(min: 1, max: 100 * 1000 * 1000),
            ),
            $this->rules(
                'interestRate',
                new NotBlank, 
                new Range(min: 0.01 / 100, max: 100 / 100),
            ),
            $this->rules(
                'duration',
                new NotBlank, 
                new Range(min: 1, max: 120),
            ),
        ));
    }
}