<?php

class BD {

		/* variables de conexion */

		var $BaseDatos;

		var $Servidor;

		var $Usuario;

		var $Clave;



		/* identificador de conexion y consulta */

		var $Conexion_ID = 0;

		var $Consulta_ID = 0;



		/* texto error */

		var $Error = false;

		var $ID_Error;



		function BD() {

			$this->BaseDatos = "vestmed";

			$this->Servidor = "200.63.96.137";

			$this->Usuario = "usrwebvestmed";

			$this->Clave = "microweb2009";

		}





		/*Conexion a la base de datos*/

		function conectar(){

			$this->Error = false;

			// Conectamos al servidor

			$this->Conexion_ID = mssql_connect($this->Servidor, $this->Usuario, $this->Clave);

			if (!$this->Conexion_ID) {

				$this->Error = "Ha fallado la conexion";

				return 0;

			}

			mssql_select_db($this->BaseDatos);

			return $this->Conexion_ID;

		}



		/*Desonexi0n a la base de datos*/

		function desconectar(){

			mssql_close($this->Conexion_ID);

		}



		/*Limpia una consulta realizada*/

		function limpiar(){

			$this->Error = false;

			mssql_free_result ($this->Consulta_ID);

		}



		function consulta($sql = ""){

			if ($sql == "") {

				$this->Error = true;

				return 0;

			}

			$this->Consulta_ID = mssql_query($sql, $this->Conexion_ID);

			if (!$this->Consulta_ID) {

				$this->Error = true;

				echo $sql;

			}

			return $this->Consulta_ID;

		}



		/* Devuelve el n�mero de campos de una consulta */

		function numcampos() {

			return mssql_num_fields($this->Consulta_ID);

		}



		/* Devuelve el n�mero de registros de una consulta */

		function numregistros(){

			return mssql_num_rows($this->Consulta_ID);

		}



		/* Devuelve el nombre de un campo de una consulta */

		function nombrecampo($numcampo) {

			return mssql_field_name($this->Consulta_ID, $numcampo);

		}



		function fetch_row($num_row){

			return mssql_fetch_assoc($this->Consulta_ID);

		}



		function affected_rows(){

			return mssql_affected_rows($this->Conexion_ID);

		}



		function last_id(){

			return mssql_insert_id($this->Conexion_ID);

		}



		function get_error(){

			return $this->Error;

		}

}



