<?php

namespace PhpBoot\Di\Scanner\Model;

enum ConstructorInjectionType: string
{
 case BEAN = 'BEAN';
 case PROPERTY = 'PROPERTY';
}
