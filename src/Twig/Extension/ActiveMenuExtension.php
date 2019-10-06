<?php
namespace App\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActiveMenuExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('activeMenu', [$this, 'activeMenu']),
            new TwigFunction('collapseItem', [$this, 'collapseItem']),
        ];
    }

    /**
     * Pass route names. If one of route names matches current route, this function returns
     * 'active'
     * @param array $routesToCheck
     * @return string
     */
    public function activeMenu(array $routesToCheck)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (empty($currentRequest)) {
            return '';
        }

        $currentRoute = $currentRequest->get('_route');

        foreach ($routesToCheck as $routeToCheck) {
            if ($routeToCheck == $currentRoute) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Pass route names. If one of route names matches current route, this function returns
     * 'active'
     * @param array $routesToCheck
     * @return string
     */
    public function collapseItem(array $routesToCheck)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (empty($currentRequest)) {
            return '';
        }

        $currentRoute = $currentRequest->get('_route');

        foreach ($routesToCheck as $routeToCheck) {
            if ($routeToCheck == $currentRoute) {
                return 'show';
            }
        }

        return '';
    }
}
