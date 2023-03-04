<?php
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpClassConstantAccessedViaChildClassInspection */
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUnused */

namespace __namespaceapi__;
use \FilmTextRepo;
use eftec\ApiAssembler\ApiAssemblerRuntime;
use Exception;
use RuntimeException;

/**
 * class FilmTextApiController
 *
 * @see           https://github.com/EFTEC/ApiAssembler
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. Dual Licence GPL-v3 and Commercial  https://github.com/EFTEC/ApiAssembler
 * @version       1.0 (2022-03-07T08:48:23Z)
 */
class FilmTextApiController
{
    /** @var ApiAssemblerRuntime */
    protected $api;

    public function __construct($api=null)
    {
        // injecting service classes.
        $this->api = $api;
    }

    /**
     * [] FilmText/listall
     * @param string $id
     * @param string $idparent
     * @param string $event the event captured by the url param "_event"
     * @return void
     */
    public function listallAction($id = null, $idparent = null, $event = null): void
    {
        $auth=$this->api->getAuth(__METHOD__, $id, $idparent, $event);
        if ($auth===false) {
            $this->api->errorShow(403, 'not allowed', 'FilmText/listall');
            die(1);
        }
        $body = $this->api->routeOne->getBody(true);
        try {
            // you can edit this part
            $result = FilmTextRepo::toList();
            // end of the edit
            } catch (Exception $e) {
            $this->api->errorShow(500, 'unable to listall FilmText: '
                . $e->getMessage(), 'FilmText/listall');
            die(1);
        }
        $this->api->messageShow($result);
    }

}
