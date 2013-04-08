<?php
echo $this->Layout->defaultHeaderMenu($catalogItemImage['CatalogItemImage']['id']);
echo $this->CatalogItem->thumb($catalogItemImage['CatalogItemImage'], array('class' => false));
