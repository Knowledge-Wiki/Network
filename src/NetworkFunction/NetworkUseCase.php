<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	private $presenter;

	public function __construct( NetworkPresenter $presenter ) {
		$this->presenter = $presenter;
	}

	public function run( RequestModel $request ): void {
		$keyValuePairs = $this->parserArgumentsToKeyValuePairs( $request->functionArguments );

		$response = new ResponseModel();
		$response->pageNames = $this->getPageNames( $request );

		if ( count( $response->pageNames ) > 100 ) {
			$this->presenter->showTooManyPagesError();
			return;
		}

		$response->cssClass = $this->getCssClass( $keyValuePairs );
		$response->excludedPages = $this->getExcludedPages( $keyValuePairs );

		$this->presenter->showGraph( $response );
	}

	/**
	 * @param string[] $arguments
	 * @return string[]
	 */
	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			[$key, $value] = $this->argumentStringToKeyValue( $argument );

			if ( !is_null( $key ) ) {
				$pairs[$key] = $value;
			}
		}

		return $pairs;
	}

	private function argumentStringToKeyValue( string $argument ): array {
		if ( false === strpos( $argument, '=' ) ) {
			return [null, $argument];
		}

		[$key, $value] = explode( '=', $argument );
		return [trim($key), trim($value)];
	}

	/**
	 * @param RequestModel $request
	 * @return string[]
	 */
	private function getPageNames( RequestModel $request ): array {
		$pageNames = [];

		foreach ( $request->functionArguments as $argument ) {
			[$key, $value] = $this->argumentStringToKeyValue( $argument );

			if ( $value !== '' && ( is_null( $key ) || $key === 'page' ) ) {
				$pageNames[] = $value;
			}
		}

		if ( $pageNames === [] ) {
			$pageNames[] = $request->renderingPageName;
		}

		return $pageNames;
	}

	private function getCssClass( array $arguments ): string {
		return trim( 'network-visualization ' . ( trim( $arguments['class'] ?? '' ) ) );
	}

	/**
	 * @param string[] $arguments
	 * @return string[]
	 */
	private function getExcludedPages( array $arguments ): array {
		return array_filter(
			array_map(
				'trim',
				explode( ';', $arguments['exclude'] ?? '' )
			),
			function( string $pageName ) {
				return $pageName !== '';
			}
		);
	}

}
