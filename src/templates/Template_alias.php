<?php http_response_code(404); die(1); // it is a template, it is protected to be called directly ?>
    /**
     * [__verb__] __class__/__name__ alias of => __class__/__argument1__
     * @param string $id
     * @param string $idparent
     * @param string $event the event captured by the url param "_event"
     * @return void
     */
    public function __name__Action__verb__($id = null, $idparent = null, $event = null): void
    {
        $verbs=['POST','GET','PUT','DELETE',''];
        // [EDIT:content] you can edit the next lines with your custom code
        foreach($verbs as $verb) {
            $method='__argument1__Action'.$verb;
            if(method_exists($this,$method)) {
                $this->$method($id, $idparent, $event);
                break;
            }
        }
        // [/EDIT] end of the editable code
    }
