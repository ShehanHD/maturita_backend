<?php

interface HttpMapping
{
    function getMapping($PARAMS, $BODY);
    function postMapping($PARAMS, $BODY);
    function deleteMapping($PARAMS, $BODY);
    function patchMapping($PARAMS, $BODY);
}
