<?php

	class EasypackInstallProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		public $required = ['name', 'core', 'version'];
		public $unique = ['name', 'core'];

		public function process()
		{
			$primaryKeyValue = $this->getProperty($this->primaryKeyField);
			/** @var EasypackExtras $obj */
			$obj = $this->modx->getObject($this->classKey, [$this->primaryKeyField => $primaryKeyValue]);
			if ($obj instanceof EasypackExtras) {
				$this->tstart = microtime(TRUE);
				if ($err = $obj->install()) {
					$this->tend = microtime(TRUE);
					$this->time = " <strong>$this->PKG_NAME</strong> time: <strong>" . round(($this->tend - $this->tstart) * 1000) . '</strong> ms';
					$send = $obj->toArray();
					$send['time'] = round(($this->tend - $this->tstart) * 1000);
					$this->sendAuthorStat([
						'action' => 'install',
						'componentName' => $obj->get('name'),
						'data' => $send,
					]);
					return $this->success($this->time);
				} else {
					$this->failure($err);
				}
			} else {
				return $this->failure($primaryKeyValue . ' not found');
			}
		}

		function sendAuthorStat($data)
		{
			$curl = curl_init();
			$data = array_merge(['componentName' => 'easypack', 'site' => $_SERVER['SERVER_NAME']], $data);

			$data = json_encode($data);
			curl_setopt_array($curl, [
				CURLOPT_URL => 'http://traineratwot.aytour.ru/component/stat',
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_AUTOREFERER => true,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HEADER => 0,
			]);

			curl_exec($curl);
			curl_close($curl);
		}
	}


	return 'EasypackInstallProcessor';
