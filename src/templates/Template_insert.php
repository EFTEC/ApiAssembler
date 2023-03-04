<?php http_response_code(404); die(1); // it is a template, it is protected to be called directly ?>
    /**
     * [__verb__] __class__/__name__
     * @param string $id
     * @param string $idparent
     * @param string $event the event captured by the url param "_event"
     * @return void
     */
    public function __name__Action__verb__($id = null, $idparent = null, $event = null): void
    {
        $auth=$this->api->getAuth('__class__/__name__', $id, $idparent, $event);
        if ($auth===false) {
            $this->api->errorShow(401, 'Unauthorized', '__class__/__name__', $this->api->auth->failCause);
            die(1);
        }
        try {
            // [EDIT:__name__Action__verb__] you can edit this part
            __body__
            $result = __class____classpostfix__::useCache(__cache__)->insert($body);
            $this->api->messageShow($result);
            // [/EDIT] end of the edit
            } catch (Exception $e) {
                $this->api->errorShow(500, 'unable to __name__ __class__: '
                   , '__class__/__name__', [$e->getMessage()]);
            die(1);
        }
    }
