<?php

namespace Oro\Bundle\EntityBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @RouteResource("entity_data")
 * @NamePrefix("oro_api_")
 */
class EntityDataController extends FOSRestController
{
    /**
     * Patch entity field/s data by new values
     *
     * @param int $id
     * @param int $className
     *
     * @return Response
     *
     * @throws AccessDeniedException
     *
     * @Rest\Patch("entity/{className}/{id}")
     * @ApiDoc(
     *      description="Update entity property",
     *      resource=true,
     *      requirements = {
     *          {"name"="id", "dataType"="integer"},
     *      }
     * )
     */
    public function patchAction($className, $id)
    {
        try {
            $data = json_decode($this->get('request_stack')->getCurrentRequest()->getContent(), true);
            $data = $this->getManager()->patch($className, $id, $data);

            $view = $this->view($data, Codes::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $view = $this->view($e->getMessage(), $e->getCode());
        }
        $response = parent::handleView($view);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('oro_entity.manager.api.enitty_data_api_manager');
    }
}
