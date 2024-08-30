<?php

namespace Drupal\brimw\External;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class SimulationRemoteData extends BaseRemoteData 
{
  protected function gqlUrl(): string
  {
    return $_ENV['SIMULATION_URL'];
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
              interstRate
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
    $interestRate = $static['kprScheme']['interestRate'] ?? 0.05;
    $query = <<< GRAPHQL
      mutation {
        estimateKpr (input: {
          installmentAmount: {$request->get('installmentAmount')}
          installmentTerm: {$request->get('installmentTerm')}
          interestRate: {$interestRate}
        }
        ) {
          monthlyInstallment
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateKpr'] ?? [];
  }

  public function estimateKprs(Request $request): array
  { 
    $static = $this->getMasterData('kprs');
    $interestRate = $static['kprsScheme']['interestRate'] ?? 0.05;
    $query = <<< GRAPHQL
      mutation {
        estimateKprs (input: {
          installmentAmount: {$request->get('installmentAmount')}
          installmentTerm: {$request->get('installmentTerm')}
          interestRate: {$interestRate}
        }
        ) {
          monthlyInstallment
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateKprs'] ?? [];
  }

  // estimateBritamaRencana

  // estimateBriguna

  public function estimateBrigunaKarya(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        estimateBrigunaKarya (input: {
          salary: {$request->get('salary')}
          installmentTerm: {$request->get('installmentTerm')}
          interestRate: {$request->get('interestRate')}
        }
        ) {
          monthlyInstallment
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateBrigunaKarya'] ?? [];
  }

  public function estimateBrigunaPurna(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        estimateBrigunaPurna (input: {
          salary: {$request->get('salary')}
          installmentTerm: {$request->get('installmentTerm')}
          interestRate: {$request->get('interestRate')}
        }
        ) {
          monthlyInstallment
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateBrigunaPurna'] ?? [];
  }

  public function estimateDeposito(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        estimateDeposito (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateDeposito'] ?? [];
  }

  public function estimateDepositoValas(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        estimateDepositoValas (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateDepositoValas'] ?? [];
  }

  public function estimateDepositoBusiness(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
        estimateDepositoBusiness (input: {
          termInMonths: {$request->get('termInMonths')}
          depositAmount: {$request->get('depositAmount')}
        }
        ) {
          totalInterest
          totalDeposit
          totalDepositWithInterest
        }
      }
    GRAPHQL;

    return $this->gql($query)['data']['estimateDepositoBusiness'] ?? [];
  }

  // estimateInvestmentDPLK

  public function estimateInvestment(Request $request): array
  {
    $query = <<< GRAPHQL
      mutation {
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

    return $this->gql($query)['data']['estimateInvestment'] ?? [];
  }
}
