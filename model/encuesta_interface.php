<?php

require_once "conexion.php";
require_once "encuesta.php";

class ORM_encuesta{

public static function buscar_encuesta($id_encuesta)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT * FROM encuesta WHERE id_encuesta=?",array($id_encuesta));
    $row = $query[0];

    $encuesta = new Encuesta();
    /*
    $encuesta->setId_encuesta($row['id_encuesta']);
    $encuesta->setfecha_inicio($row['fecha_inicio']);
    $encuesta->setfecha_cierre($row['fecha_cierre']);
    $encuesta->setid_resultado($row['id_resultado']);
    $encuesta->setId_rol($row['id_rol']);
    */
    // implementacion del metodo init
    $encuesta->init($row['id_encuesta'],$row['fecha_inicio'],$row['fecha_cierre'],$row['id_resultado']);
    return $encuesta;
  }

public static function buscar_encuesta_fechas($fecha_inicio, $fecha_cierre)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta_fetch("SELECT id_encuesta FROM encuesta WHERE fecha_inicio=? and fecha_cierre=?",array($fecha_inicio,$fecha_cierre));
    $id_encuesta = $query['id_encuesta'];
    return (int)$id_encuesta;
  }

public static function obtener_todos_encuesta()
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT * FROM encuesta");
    return $query;
  }

public static function obtener_todos_encuesta_en_termino()
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT * FROM encuesta WHERE encuesta.`fecha_cierre` > CURRENT_DATE ");
    return $query;
  }

/*
public static function agregar_encuesta($encuesta)
  {
    $conexion = new Conexion();
    $sql_insert = "INSERT INTO encuesta (fecha_inicio, fecha_cierre, id_resultado, id_rol, activo) VALUES (?,?,?,?)";
    $campos = array($encuesta->getfecha_inicio(), $encuesta->getfecha_cierre(), $encuesta->getid_resultado(), $encuesta->getId_rol(), $encuesta->getActivo());
    $query = $conexion->consulta_row($sql_insert,$campos);
    return $query;
  }
*/
public static function agregar_encuesta_campos($fecha_inicio, $fecha_cierre, $id_resultado)
  {
    $conexion = new Conexion();
    $sql_insert = "INSERT INTO encuesta (fecha_inicio, fecha_cierre, id_resultado) VALUES (?,?,?)";
    $campos = array($fecha_inicio, $fecha_cierre, $id_resultado);
    $query = $conexion->consulta_row($sql_insert,$campos);
    return $query;
  }

public static function eliminar_encuesta($id_encuesta)
  {
    $conexion = new Conexion();
    $sql_delete = "delete from encuesta where id_encuesta=?";
    $campos = array($id_encuesta);
    $query = $conexion->consulta_row($sql_delete,$campos);
    return $query;
  }


public static function actualizar_encuesta($encuesta)
  {
    $conexion = new Conexion();
    $sql_update = "UPDATE encuesta SET fecha_inicio=?,fecha_cierre=?,id_resultado=? WHERE id_encuesta=?";
    $campos = array($encuesta->getfecha_inicio(), $encuesta->getfecha_cierre(), $encuesta->getId_resultado(), $encuesta->getId_encuesta());
    $query = $conexion->consulta_row($sql_update,$campos);
    return $query;
  }
  
public static function buscar_por_fechaInicio($fecha_inicio)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta_fetch("SELECT id_encuesta FROM encuesta WHERE fecha_inicio=?",array($fecha_inicio));
    $id_encuesta = $query['id_encuesta'];
    return (int)$id_encuesta;
  }

public static function buscar_por_resultado($id_resultado)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta_fetch("SELECT id_encuesta FROM encuesta WHERE id_resultado=?",array($id_resultado));
    $id_encuesta = $query['id_encuesta'];
    return (int)$id_encuesta;
  }
	
public static function agregar_encuesta($fecha_inicio, $fecha_cierre, $id_resultado)
  {
		$existe = ORM_encuesta::buscar_encuesta_fechas($fecha_inicio, $fecha_cierre);
    if (!$existe){
      $row_affected = ORM_encuesta::agregar_encuesta_campos($fecha_inicio, $fecha_cierre, $id_resultado);
  	  return $row_affected;
	  }
		return 0;
  }

public static function buscar_encuesta_Twig($id_encuesta)
  {
    $conexion = new Conexion();
    $encuesta = $conexion->consulta_fetch("SELECT * FROM encuesta WHERE id_encuesta=?",array($id_encuesta));
    return $encuesta;
  }

public static function buscar_encuesta_resultado_Twig($id_encuesta)
  {
    $conexion = new Conexion();
    $encuesta = $conexion->consulta("SELECT *, IF(resultado.id_resultado IN
                                    (SELECT id_resultado FROM encuesta
                                     WHERE id_encuesta = ?), 'selected', '') AS activo
                                     FROM resultado",array($id_encuesta));
    return $encuesta;
  }

public static function buscar_encuesta_Twig2($id_encuesta)
  {
    $conexion = new Conexion();
    $encuesta = $conexion->consulta_fetch("SELECT encuesta.id_encuesta, 
      fecha_inicio, 
      fecha_cierre,  
      resultado.id_resultado, 
      resultado.comentario, 
      resultado.fecha_recepcion, 
      resultado.fecha_analisis, 
      resultado.fecha_ingreso, 
      laboratorio.id_lab AS labs,
      metodo.descripcion AS descmetodo,
      reactivo.descripcion AS descreactivo,
      calibrador.descripcion AS desccalibrador,
      analito.descripcion AS descanalito,
      papel_filtro.descripcion AS descpapel_filtro,
      valor_corte.descripcion AS descvalor_corte
      FROM encuesta INNER JOIN resultado ON encuesta.id_resultado = resultado.id_resultado
      INNER JOIN laboratorio ON resultado.id_lab = laboratorio.id_lab
      INNER JOIN metodo ON resultado.id_metodo = metodo.id_metodo
      INNER JOIN reactivo ON resultado.id_reactivo = reactivo.id_reactivo
      INNER JOIN calibrador ON calibrador.id_calibrador = resultado.id_calibrador
      INNER JOIN analito ON resultado.id_analito = analito.id_analito
      INNER JOIN papel_filtro ON resultado.id_papel_filtro = papel_filtro.id_papel_filtro
      INNER JOIN valor_corte ON resultado.id_valor = valor_corte.id_valor
      INNER JOIN inscripcion ON inscripcion.`laboratorio_id_lab` = laboratorio.`id_lab`
      WHERE encuesta.id_encuesta = ?", array($id_encuesta));

      return $encuesta;
  }

  public static function mostrar_encuestas_laboratorio($id_lab)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta(
      "SELECT id_encuesta, fecha_inicio, fecha_cierre, fecha_recepcion, fecha_analisis, fecha_ingreso
      FROM encuesta AS e
      INNER JOIN resultado AS r ON r.id_resultado = e.id_resultado
      INNER JOIN laboratorio AS l ON r.id_lab = l.id_lab
      WHERE l.id_lab = ?", array($id_lab));
      return $query;
  }

 public static function buscar_encuesta_Twig_Tabla_para_lab($codlab)
  {
    $conexion = new Conexion();
    $encuesta = $conexion->consulta("SELECT id_encuesta, 
      fecha_inicio, 
      fecha_cierre,  
      resultado.id_resultado, 
      resultado.comentario, 
      resultado.fecha_recepcion, 
      resultado.fecha_analisis, 
      resultado.fecha_ingreso, 
      laboratorio.id_lab,
      metodo.descripcion AS descmetodo,
      reactivo.descripcion AS descreactivo,
      calibrador.descripcion AS desccalibrador,
      analito.descripcion AS descanalito,
      papel_filtro.descripcion AS descpapel_filtro,
      valor_corte.descripcion AS descvalor_corte
                  FROM encuesta 
                  INNER JOIN resultado ON encuesta.id_resultado = resultado.id_resultado INNER JOIN laboratorio ON resultado.id_lab = laboratorio.id_lab INNER JOIN metodo ON resultado.id_metodo = metodo.id_metodo INNER JOIN reactivo ON resultado.id_reactivo = reactivo.id_reactivo INNER JOIN calibrador ON calibrador.id_calibrador = resultado.id_calibrador INNER JOIN analito ON resultado.id_analito = analito.id_analito INNER JOIN papel_filtro ON resultado.id_papel_filtro = papel_filtro.id_papel_filtro INNER JOIN valor_corte ON resultado.id_valor = valor_corte.id_valor
    WHERE id_encuesta = ? AND laboratorio.cod_lab = $cod_lab", array($id_encuesta));
                 
    return $encuesta;
  }

  public static function obtener_estadisticas_encuestas()
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT encuesta.`id_encuesta`, encuesta.`fecha_inicio`, encuesta.`fecha_cierre`
                                  FROM laboratorio INNER JOIN inscripcion
                                  ON laboratorio.`id_lab` = inscripcion.`laboratorio_id_lab`
                                  INNER JOIN encuesta ON  inscripcion.`id_encuesta` = encuesta.`id_encuesta`
                                  WHERE laboratorio.`estado` = 1 
                                  AND inscripcion.`fecha_baja` <= encuesta.`fecha_cierre` 
                                  GROUP BY encuesta.`fecha_inicio` , fecha_cierre , encuesta.`id_encuesta` ");
    return $query;
  }

    public static function participantes_encuestas($id_encuesta)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT cod_lab, inscripcion.`id_inscripcion` FROM laboratorio
                                  INNER JOIN inscripcion ON laboratorio.id_lab = inscripcion.`laboratorio_id_lab`
                                  INNER JOIN encuesta ON inscripcion.`id_encuesta` = encuesta.`id_encuesta`
                                  WHERE encuesta.id_encuesta = ? GROUP BY inscripcion.`id_inscripcion`, encuesta.`id_resultado`", array($id_encuesta));
    return $query;
  }

  public static function  obtener_compraciones_encuestas_validas($idLab = null)
  {
    $conexion = new Conexion();
	$sql = "SELECT laboratorio.`id_lab`, laboratorio.`cod_lab`, encuesta.`id_encuesta`, encuesta.`fecha_inicio`, encuesta.`fecha_cierre` FROM laboratorio
                                  INNER JOIN inscripcion ON laboratorio.`id_lab` = inscripcion.`laboratorio_id_lab`
                                  INNER JOIN encuesta ON  inscripcion.`id_encuesta` = encuesta.`id_encuesta`
                                  WHERE encuesta.`fecha_cierre` < CURRENT_DATE()
                                  AND laboratorio.`estado` = 1 AND inscripcion.`fecha_baja` <= encuesta.`fecha_cierre` 
                                  AND EXISTS (SELECT * FROM laboratorio INNER JOIN resultado ON laboratorio.`id_lab` = resultado.`id_lab` WHERE laboratorio.`id_lab` = 0)
								  AND laboratorio.id_lab <> 0";
	if ($idLab != null){
		$sql .= ' AND laboratorio.`id_lab`=?';
		$parametros = array($idLab);
	}
	else{
		$parametros = array();
	}
	
    $query = $conexion->consulta($sql,$parametros);
    return $query;
  }

  public static function obtener_resultados_muestra($id_encuesta, $id_lab=0)
  {
    $conexion = new Conexion();
    $query = $conexion->consulta("SELECT * FROM resultado INNER JOIN muestra ON (resultado.id_resultado=muestra.id_resultado)
                                  INNER JOIN encuesta ON resultado.id_resultado=encuesta.id_resultado WHERE id_encuesta= ?
                                  AND id_lab= ?", array($id_encuesta, $id_lab));
    return $query;
  }
  
    public static function obtener_resultados_muestra_desc($id_encuesta, $id_lab=0)
  {
    $conexion = new Conexion();
	$sql = "SELECT 
			resultado.id_resultado,
			resultado.comentario,
			analito.descripcion AS analito,
			metodo.descripcion AS metodo,
			reactivo.descripcion AS reactivo,
			papel_filtro.descripcion AS papel_filtro,
			valor_corte.descripcion AS valor_de_corte,
			calibrador.descripcion AS calibrador,
			decision.descripcion AS decision,
			interpretacion.descripcion AS interpretacion,
			muestra.id_muestra AS nro_muestra,
			muestra.resultado_control

			FROM resultado INNER JOIN muestra ON (resultado.id_resultado=muestra.id_resultado)
						   INNER JOIN encuesta ON (resultado.id_resultado=encuesta.id_resultado) 
						   INNER JOIN analito ON (resultado.id_analito=analito.id_analito)
						   INNER JOIN metodo ON (resultado.id_metodo=metodo.id_metodo)
						   INNER JOIN calibrador ON (resultado.id_calibrador=calibrador.id_calibrador)
						   INNER JOIN reactivo ON (resultado.id_reactivo=reactivo.id_reactivo)
						   INNER JOIN papel_filtro ON (resultado.id_papel_filtro=papel_filtro.id_papel_filtro)
						   INNER JOIN valor_corte ON (resultado.id_valor=valor_corte.id_valor)
						   INNER JOIN interpretacion ON (muestra.id_interpretacion=interpretacion.id_interpretacion)
						   INNER JOIN decision ON (muestra.id_decision=decision.id_decision)

			WHERE id_encuesta = ? AND id_lab = ?";
    $parametros =  array($id_encuesta, $id_lab);
	$query = $conexion->consulta($sql,$parametros);
    return $query;
  }

}
?>
