<?php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Customer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomerNormalizer implements NormalizerInterface
{
    public function __construct(){

    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        return [];
    }

//    public function __construct(private NormalizerInterface $decorated, private $circularReferenceHandler)
//    {}
//
//    public function normalize($object, string $format = null, array $context = []): array
//    {
//        if ($object instanceof Customer) {
//           $data = $this->decorated->normalize($object, $format, $context);
//            if ($object->getPhoto() instanceof File) {
//                $data['photo'] = $object->getPhoto()->getPathname();
//            }
//
//            return $data;
//        }
//
//
//        return $this->decorated->normalize($object, $format, $context);
//    }
//
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Customer;
    }
}
