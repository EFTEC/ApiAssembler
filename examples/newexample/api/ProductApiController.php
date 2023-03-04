<?php
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUnused */

namespace examples\newexample\api;
use examples\newexample\repo\ProductRepo;
use eftec\ApiAssembler\ApiAssemblerRuntime;
use Exception;
use RuntimeException;

/**
 * class ProductApiController
 *
 * @see           https://github.com/EFTEC/ApiAssembler
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. Dual Licence GPL-v3 and Commercial  https://github.com/EFTEC/ApiAssembler
 * @version       1.0 (2022-02-11T11:34:47Z)
 */
class ProductApiController
{
    /** @var ApiAssemblerRuntime */
    protected $api;

    public function __construct($api=null)
    {
        // injecting service classes.
        $this->api = $api;
    }

    /**
     * [] Product/listall
     * @param string $id
     * @param string $idparent
     * @param string $event the event captured by the url param "_event"
     * @return void
     */
    public function listallAction($id = null, $idparent = null, $event = null): void
    {
        $auth=$this->api->getAuth(__METHOD__, $id, $idparent, $event);
        if ($auth===false) {
            $this->api->errorShow(403, 'not allowed', 'Product/listall');
            die(1);
        }
        $body = $this->api->routeOne->getBody(true);
        try {
            // you can edit this part
            $result = ProductRepo::toList();
            // end of the edit
            } catch (Exception $e) {
            $this->api->errorShow(500, 'unable to listall Product: '
                . $e->getMessage(), 'Product/listall');
            die(1);
        }
        $this->api->messageShow($result);
    }

}
