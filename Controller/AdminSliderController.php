<?php

namespace SmartCore\Module\Slider\Controller;

use SmartCore\Module\Slider\Entity\Slide;
use SmartCore\Module\Slider\Form\Type\SlideCreateFormType;
use SmartCore\Module\Slider\Form\Type\SlideEditFormType;
use SmartCore\Module\Slider\Form\Type\SliderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class AdminSliderController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $sliderService = $this->get('smart_module.slider');

        $form = $this->createForm(SliderFormType::class);
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn btn-success']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $sliderService->createSlider($form->getData());
                $this->get('session')->getFlashBag()->add('success', 'Слайдер создан');

                return $this->redirect($this->generateUrl('smart_module.slider.admin'));
            }
        }

        return $this->render('SliderModule:Admin:index.html.twig', [
            'form'    => $form->createView(),
            'sliders' => $sliderService->allSliders(),
        ]);
    }

    /**
     * @param  Request $request
     * @param  int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \RuntimeException
     */
    public function sliderAction(Request $request, $id)
    {
        // @todo сделать загрузку оригинальных картинок в /app/usr/SmartSliderModule
        /*
        $usrDir = $this->container->getParameter('kernel.root_dir') . '/usr';

        $sliderOriginalDir = '/SmartSliderModule';

        $usrDir .= $sliderOriginalDir;

        if (!is_dir($usrDir) and false === @mkdir($usrDir, 0777, true)) {
            throw new \RuntimeException(sprintf("Unable to create the %s directory (%s)\n", $sliderOriginalDir, $usrDir));
        }
        */

        // -------------
        $sliderService = $this->get('smart_module.slider');

        $slider = $sliderService->getSlider($id);

        $slide = new Slide();
        $slide->setSlider($slider);

        $form = $this->createForm(SlideCreateFormType::class, $slide);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid() and null !== $form->get('file')->getData()) {
                $sliderService->save($form->getData());
                $this->get('session')->getFlashBag()->add('success', 'Слайд создан');

                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $slider->getId()]));
            }
        }

        $folderPath = null;
        foreach ($this->get('cms.node')->findByModule('Slider') as $node) {
            if ($node->getParam('slider_id') === (int) $id) {
                $folderPath = $this->get('cms.folder')->getUri($node);

                break;
            }
        }

        return $this->render('SliderModule:Admin:slider.html.twig', [
            'form'       => $form->createView(),
            'slider'     => $slider,
            'folderPath' => $folderPath,
            'webPath'    => $sliderService->getWebPath(),
        ]);
    }

    /**
     * @param  Request $request
     * @param  int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sliderEditAction(Request $request, $id)
    {
        $sliderService = $this->get('smart_module.slider');

        $slider = $sliderService->getSlider($id);

        $form = $this->createForm(SliderFormType::class, $slider);
        $form->add('update', SubmitType::class, ['attr' => ['class' => 'btn btn-success']]);
        $form->add('delete', SubmitType::class, ['attr' => ['class' => 'btn btn-danger', 'onclick' => "return confirm('Вы уверены, что хотите удалить слайдер?')"]]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $id]));
            }

            if ($form->get('delete')->isClicked()) {
                $sliderService->deleteSlider($form->getData());

                $this->get('session')->getFlashBag()->add('success', 'Слайдер удалён');

                return $this->redirect($this->generateUrl('smart_module.slider.admin'));
            }

            if ($form->isValid() and $form->get('update')->isClicked()) {
                $sliderService->updateSlider($form->getData());
                $this->get('session')->getFlashBag()->add('success', 'Слайдер обновлён');

                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $id]));
            }
        }

        return $this->render('SliderModule:Admin:slider_edit.html.twig', [
            'form'    => $form->createView(),
            'slider'  => $slider,
        ]);
    }

    /**
     * @param  int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function slideEditAction(Request $request, $id)
    {
        $sliderService = $this->get('smart_module.slider');

        $slide = $sliderService->getSlide($id);

        $form = $this->createForm(SlideEditFormType::class, $slide);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $slide->getSlider()->getId()]));
            }

            if ($form->get('delete')->isClicked()) {
                $sliderService->deleteSlide($form->getData());
                $this->get('session')->getFlashBag()->add('success', 'Слайд удалён');

                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $slide->getSlider()->getId()]));
            }

            if ($form->isValid() and $form->get('update')->isClicked()) {
                $sliderService->updateSlide($form->getData());
                $this->get('session')->getFlashBag()->add('success', 'Слайд обновлён');

                return $this->redirect($this->generateUrl('smart_module.slider.admin_slider', ['id' => $slide->getSlider()->getId()]));
            }
        }

        return $this->render('SliderModule:Admin:slide_edit.html.twig', [
            'form'  => $form->createView(),
            'slide' => $slide,
            'webPath' => $sliderService->getWebPath(),
        ]);
    }
}
