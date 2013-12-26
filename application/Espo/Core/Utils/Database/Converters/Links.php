<?php

namespace Espo\Core\Utils\Database\Converters;

use Espo\Core\Utils\Util;

class Links
{
    private $metadata;

    private $relations;


	public function __construct(\Espo\Core\Utils\Metadata $metadata)
	{
		$this->metadata = $metadata;

		$this->relations = new Relations($this->metadata);
	}

	protected function getMetadata()
	{
		return $this->metadata;
	}

	protected function getRelations()
	{
		return $this->relations;
	}


	public function getLinkEntityName($entityName, $link)
	{
		if (isset($link['params'])) {
        	return isset($link['params']['entity']) ? $link['params']['entity'] : $entityName;
		}

		return isset($link['entity']) ? $link['entity'] : $entityName;
	}

	public function isMethodExists($methodName)
	{
		if (method_exists($this, $methodName) || method_exists($this->getRelations(), $methodName)) {
        	return true;
		}

		return false;
	}


	public function process($method, $entityName, $link, $foreignLink = array())
	{
		$params = array();
		$params['entityName'] = $entityName;
        $params['link'] = $link;

        $foreignParams = array();
		$foreignParams['entityName'] = $this->getLinkEntityName($entityName, $link);
		$foreignParams['link'] = $foreignLink;

		//$params['targetEntity'] = $this->getMetadata()->getEntityPath($foreignParams['entityName']);
		//$foreignParams['targetEntity'] = $this->getMetadata()->getEntityPath($params['entityName']);
		$params['targetEntity'] = $foreignParams['entityName'];
		$foreignParams['targetEntity'] = $params['entityName'];

        //hasMany With Relation Name
		if (isset($link['params'])) {
        	switch ($link['params']['type']) {
	        	case 'hasMany':
					if (isset($link['params']['relationName'])) {
                    	$method = 'hasManyWithName';
					}
					break;
	        }
		} //END: hasMany With Relation Name

		if (method_exists($this, $method)) {
        	return $this->$method($params, $foreignParams);
		} else if (method_exists($this->getRelations(), $method)) {
			return $this->getRelations()->$method($params, $foreignParams);
		}

        return false;
	}


	protected function hasManyHasMany($params, $foreignParams)
	{
    	return $this->getRelations()->manyMany($params, $foreignParams);
	}



	/*protected function belongsTo($params, $foreignParams)
	{
    	return $this->getRelations()->belongsTo($params, $foreignParams);
	}

	protected function hasMany($params, $foreignParams)
	{
    	return $this->getRelations()->hasMany($params, $foreignParams);
	}

	protected function hasChildren($params, $foreignParams)
	{
		return $this->getRelations()->hasChildren($params, $foreignParams);
	}


	protected function linkParent($params, $foreignParams)
	{
    	return $this->getRelations()->linkParent($params, $foreignParams);
	}

	protected function linkMultiple($params, $foreignParams)
	{
    	return $this->getRelations()->linkMultiple($params, $foreignParams);
	}


	protected function teamRelation($params, $foreignParams)
	{
    	return $this->getRelations()->teamRelation($params, $foreignParams);
	} */

/*
[0] => belongsTo
[1] => belongsToParent
[2] => hasMany
[3] => hasChildrenBelongsToParent
[4] => hasManyHasMany
[5] => hasOne
[6] => hasManyBelongsTo
[7] => belongsToHasMany
[8] => joint
[9] => belongsToParentHasChildren
	*/

}