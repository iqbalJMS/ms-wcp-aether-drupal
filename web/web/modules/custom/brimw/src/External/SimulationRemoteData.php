<?php

namespace Drupal\brimw\External;

use Symfony\Component\HttpFoundation\Request;
use Drupal;

class SimulationRemoteData extends BaseRemoteData 
{
  protected function gqlUrl(): string
  {
    return $_ENV['SIMULATION_URL'];
  }

  public function error($response) {
    return Drupal::service('brimw.simulation_request')->finalizeValidation(['error' => $response['errors'][0]['message'] ?? 'Server error']);
  }

  public function getAllInstallmentSchemes(): array
  {
    $query = <<< GRAPHQL
      query {
        getAllInstallmentSchemes  {
          name
          type
        }
      }
    GRAPHQL;

    return array_column($this->gql($query)['data']['getAllInstallmentSchemes'], 'name', 'type');
  }

  public function getMasterData($scheme = 'all'): array
  {
    $defaults = [
      'kpr' => <<< GRAPHQL
          kprScheme {
            interestRate
          }
        GRAPHQL,
      'kprs' => <<< GRAPHQL
          kprsScheme {
            interestRate
          }
          GRAPHQL,
      'briguna' => <<< GRAPHQL
          brigunaScheme {
            interestRate
          }
          GRAPHQL,
      'brigunaKarya' => <<< GRAPHQL
          brigunaKaryaScheme {
            interestRate
          }
          GRAPHQL,
      'brigunaPurna' => <<< GRAPHQL
          brigunaPurnaScheme {
            interestRate
          }
          GRAPHQL,
      'deposito' => <<< GRAPHQL
          depositoScheme {
            interestRate
          }
          GRAPHQL,
      'depositoValas' => <<< GRAPHQL
          depositoValasScheme {
            interestRate
          }
          GRAPHQL,
      'depositoBusiness' => <<< GRAPHQL
          depositoBusinessScheme {
            interestRate
          }
          GRAPHQL,
      'mutualFund' => <<< GRAPHQL
          mutualFundScheme {
            interestRate
          }
          GRAPHQL,
      'vehicleInstallment' => <<< GRAPHQL
          vehicleInstallmentScheme {
            id
            downPaymentPercentage
            termRateSchemes {
              installmentTerm
              interestRate
            }
          }
          GRAPHQL,
    ];

    $defaults['all'] = implode(',', $defaults);

    if (!$defaults[$scheme]) {
      return [];
    }

    $query = <<< GRAPHQL
      query {
        getMasterData {
          $defaults[$scheme]
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['getMasterData'];
  }

  public function estimateKpr(Request $request): array
  { 
    $static = $this->getMasterData('kpr');
    $query = <<< GRAPHQL
      query {
        estimateKpr (input: {
          installmentAmount: {$request->get('installmentAmount')}
          installmentTerm: {$request->get('installmentTerm')}
        }
        ) {
          monthlyInstallment
          interestRate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateKpr'] ?? $this->error($response);
  }

  public function estimateKprs(Request $request): array
  { 
    $static = $this->getMasterData('kprs');
    $query = <<< GRAPHQL
      query {
        estimateKprs (input: {
          installmentAmount: {$request->get('installmentAmount')}
          installmentTerm: {$request->get('installmentTerm')}
        }
        ) {
          monthlyInstallment
          interestRate  
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateKprs'] ?? $this->error($response);
  }

  public function estimateBritamaRencana(Request $request): array
  {
    $static = $this->getMasterData('kprs');
    $query = <<< GRAPHQL
      query {
        estimateBritamaRencana (input: {
          month: {$request->get('month')}
          amount: {$request->get('amount')}
          premiAsuransi: {$request->get('premiAsuransi')}
        }) {
          bungaSaldoBritamaRencana
          saldoTanpaBunga
          bunga
          totalInvestasiBritamaRencana
          interestRate
          asurancePremium
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateBritamaRencana'] ?? $this->error($response);
  }

  public function estimateBriguna(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateBriguna (
          karyaInput: {
            salary: {$request->get('karyaSalary')}
            installmentTerm: {$request->get('karyaInstallmentTerm')}
            InterestRate: {$request->get('karyaInterestRate')}
          }
          purnaInput: {
            salary: {$request->get('purnaSalary')}
            installmentTerm: {$request->get('purnaInstallmentTerm')}
            InterestRate: {$request->get('purnaInterestRate')}
          }
        ) {
          monthlyInstallment
          interestRate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateBriguna'] ?? $this->error($response);
  }

  public function estimateBrigunaKarya(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateBrigunaKarya (input: {
          salary: {$request->get('salary')}
          installmentTerm: {$request->get('installmentTerm')}
          InterestRate: {$request->get('interestRate')}
        }
        ) {
          monthlyInstallment
          interestRate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateBrigunaKarya'] ?? $this->error($response);
  }

  public function estimateBrigunaPurna(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateBrigunaPurna (input: {
          salary: {$request->get('salary')}
          installmentTerm: {$request->get('installmentTerm')}
          InterestRate: {$request->get('interestRate')}
        }
        ) {
          monthlyInstallment
          interestRate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateBrigunaPurna'] ?? $this->error($response);
  }

  public function estimateDeposito(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateDeposito (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
          rate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateDeposito'] ?? $this->error($response);
  }

  public function estimateDepositoValas(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateDepositoValas (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
          rate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateDepositoValas'] ?? $this->error($response);
  }

  public function estimateDepositoBusiness(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateDepositoBusiness (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
          rate
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateDepositoBusiness'] ?? $this->error($response);
  }

  public function estimateInitialInvestment(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateInitialInvestment (input: {
          targetInvestmentValue: {$request->get('targetInvestmentValue')}
          duration: {$request->get('duration')}
        }
        ) {
          oneTimeInvestmentRequired
          periodicInvestmentRequired
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateInitialInvestment'] ?? $this->error($response);
  }

  public function estimateInvestment(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateInvestment (input: {
          investmentAmount: {$request->get('investmentAmount')}
          duration: {$request->get('duration')}
          interestRate: {$request->get('interestRate')}
        }
        ) {
          oneTimeInvestmentResult
          periodicInvestmentResult
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateInvestment'] ?? $this->error($response);
  }

  public function estimateVehicleInstallment(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateVehicleInstallment (input: {
          vehiclePrice: {$request->get('vehiclePrice')}
          installmentTerm: {$request->get('installmentTerm')}
          vehicleStatus: {$request->get('vehicleStatus')}
        }) {
          vehiclePrice
          downPaymentAmount
          principalDebt
          interestRate
          principalInstallment
          interestInstallmentPerMonth
          totalInstallmentPerMonth
          administrationFee
          totalPayment
          provisionFee
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateVehicleInstallment'] ?? $this->error($response);
  }

  public function estimateObligasi(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateObligasi (input: {
          amount: {$request->get('amount')}
          term: {$request->get('term')}
          couponRate: {$request->get('couponRate')}
        }) {
          estimatedYield
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateObligasi'] ?? $this->error($response);
  }

  public function estimateReksadana(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateReksadana (input: {
          amount: {$request->get('amount')}
          investmentType: {$request->get('investmentType')}
        }) {
          estimatedYield
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateReksadana'] ?? $this->error($response);
  }

  public function estimateKreditInvestasi(Request $request): array
  {
    $query = <<< GRAPHQL
      query {
        estimateKreditInvestasi (input: {
          installmentTerm: {$request->get('installmentTerm')}
          installment: {$request->get('installment')}
          InterestRate: {$request->get('InterestRate')}
        }) {
          monthlyPrincipalInstallment
          interest
        }
      }
    GRAPHQL;

    $response = $this->gql($query);

    return $response['data']['estimateKreditInvestasi'] ?? $this->error($response);
  }
}
