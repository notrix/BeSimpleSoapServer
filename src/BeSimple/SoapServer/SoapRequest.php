<?php

/*
 * This file is part of the BeSimpleSoapClient.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapServer;

use BeSimple\SoapCommon\SoapRequest as CommonSoapRequest;
use BeSimple\SoapCommon\SoapMessage;

/**
 * SoapRequest class for SoapClient. Provides factory function for request object.
 *
 * @author Andreas Schamberger <mail@andreass.net>
 */
class SoapRequest extends CommonSoapRequest
{
    /**
     * Factory function for SoapRequest.
     *
     * @param string $content Content
     * @param string $version SOAP version
     *
     * @return BeSimple\SoapClient\SoapRequest
     */
    public static function create($content, $version)
    {
        $content = is_null($content) ? file_get_contents("php://input") : $content;
        $location = self::getCurrentUrl();
        
        $request = new SoapRequest();
        // $content is if unmodified from SoapClient not a php string type!
        $request->setContent((string) $content);
        $request->setLocation($location);
        $request->setVersion($version);
        
        switch ($version) {
            case 1:
                $request->setAction($_SERVER[SoapMessage::SOAP_ACTION_HEADER]);
                break;
            case 2:
            default:
                $request->setContentType($_SERVER[SoapMessage::CONTENT_TYPE_HEADER]);
        }
        return $request;
    }

    /**
     * Builds the current URL from the $_SERVER array.
     *
     * @return string
     */
    public static function getCurrentUrl()
    {
        $url = '';
        if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] === '1')) {
            $url .= 'https://';
        } else {
            $url .= 'http://';
        }
        $url .= isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '';
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {
            $url .= ":{$_SERVER['SERVER_PORT']}";
        }
        $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return $url;
    }
}