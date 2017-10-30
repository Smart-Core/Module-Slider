<?php

namespace SmartCore\Module\Slider\Controller;

use SmartCore\Bundle\CMSBundle\Module\NodeTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SliderController extends Controller
{
    use NodeTrait;

    /**
     * @var int
     */
    protected $slider_id;

    /**
     * @param  Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (null === $this->slider_id) {
            return new Response();
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $slider = $em->find('SliderModuleBundle:Slider', $this->slider_id);

        $this->node->addFrontControl('manage_slider')
            ->setTitle('Управление слайдами')
            ->setUri($this->generateUrl('smart_module.slider.admin_slider', ['id' => $this->slider_id]));

        return $this->get('twig')->render('SliderModuleBundle::'.$slider->getLibrary().'.html.twig', [
            'slider'  => $slider,
            // @todo настройку места хранения картинок, лучше в медиалибе!.
            'imgPath' => $request->getBasePath().'/'.$this->get('smart_module.slider')->getWebPath(),
        ]);
    }
}
