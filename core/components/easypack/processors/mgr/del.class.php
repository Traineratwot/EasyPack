<?php
	/**
	 * Created by Kirill Nefediev.
	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class EasypackDelProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';

		public function beforeSet()
		{
			return !$this->hasErrors();
		}

		public function afterRemove()
		{
			$this->sendAuthorStat([
				'action' => 'remove',
				'componentName' => $this->object->get('name'),
				'data' => $this->object->toArray(),
			]);
			return TRUE;
		}

		public 	function sendAuthorStat($data)
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

	return 'EasypackDelProcessor';
