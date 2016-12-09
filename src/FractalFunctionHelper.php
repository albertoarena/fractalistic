<?php

namespace Spatie\Fractalistic;

use Traversable;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;
use Spatie\Fractalistic\Exceptions\InvalidFractalHelperArgument;

class FractalFunctionHelper
{
    /** @var \Spatie\Fractalistic\Fractal */
    protected $fractal;

    /** @var array */
    protected $arguments;

    public function __construct(array $arguments)
    {
        $this->fractal = new Fractal(new Manager());

        $this->arguments = $arguments;
    }

    /**
     * @return \Spatie\Fractalistic\Fractal
     *
     * @throws \Spatie\Fractalistic\Exceptions\InvalidFractalHelperArgument
     */
    public function getFractalInstance()
    {
        if (count($this->arguments) === 1) {
            throw InvalidFractalHelperArgument::secondArgumentMissing();
        }

        if (count($this->arguments) >= 2) {
            $this->setData($this->arguments[0]);

            $this->setTransformer($this->arguments[1]);
        }

        if (count($this->arguments) >= 3) {
            $this->setSerializer($this->arguments[2]);
        }

        if (count($this->arguments) > 3) {
            throw InvalidFractalHelperArgument::tooManyArguments($this->arguments);
        }

        return $this->fractal;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $dataType = $this->determineDataType($data);

        $this->fractal->data($dataType, $data);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function determineDataType($data)
    {
        if (is_array($data)) {
            return 'collection';
        }

        if ($data instanceof Traversable) {
            return 'collection';
        }

        return 'item';
    }

    /**
     * @param callable|\League\Fractal\TransformerAbstract $transformer
     */
    protected function setTransformer($transformer)
    {
        $this->fractal->transformWith($transformer);
    }

    /**
     * @param \League\Fractal\Serializer\SerializerAbstract $serializer
     */
    protected function setSerializer(SerializerAbstract $serializer)
    {
        $this->fractal->serializeWith($serializer);
    }
}
