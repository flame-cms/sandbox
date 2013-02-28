<?php
/**
 * ImportPresenter.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    18.07.12
 */

namespace Flame\CMS\AdminModule;

use Flame\Utils\Strings;

class ImportPresenter extends AdminPresenter
{

	private $wordPressImporter;

	private $postFacade;

	private $userFacade;

	private $categoryFacade;

	private $tagFacade;

	private $imageStorage;

	private $downloadImages = true;

	private $dirName;

	public function __construct(
		\Flame\Addons\WordPress\PostsImporter $wordPressImporter,
		\Flame\CMS\Models\Posts\PostFacade $postFacade,
		\Flame\CMS\Models\Users\UserFacade $userFacade,
		\Flame\CMS\Models\Categories\CategoryFacade $categoryFacade,
		\Flame\CMS\Models\Tags\TagFacade $tagFacade
	)
	{
		$this->wordPressImporter = $wordPressImporter;
		$this->postFacade = $postFacade;
		$this->userFacade = $userFacade;
		$this->categoryFacade = $categoryFacade;
		$this->tagFacade = $tagFacade;
	}

	public function startup()
	{
		parent::startup();

		$this->imageStorage = $this->getContextParameter('imageStorage');

		if(!$this->imageStorage){
			throw new \Nette\Application\BadRequestException;
		}else{
			$this->dirName = $this->imageStorage['baseDir'] . DIRECTORY_SEPARATOR . $this->imageStorage['importImageDir'];
		}
	}

	protected function createComponentImportForm()
	{
		$form = new ImportForm();
		$form->configure();
		$form->onSuccess[] = callback($this, 'importFormSubmitted');
		return $form;
	}

	public function importFormSubmitted(ImportForm $form)
	{
		$values = $form->getValues();

		$this->downloadImages = (bool) $values['downloadImages'];
		if($this->downloadImages) $this->createDirForImages();

		if(!$values['file']->isOk()){
			$this->flashMessage('Uploaded file is not valid. '. $values['file']->getError());
		}else{
			$posts = $this->wordPressImporter->convert($values['file']->getTemporaryFile());

			if(is_array($posts) and count($posts)){
				foreach($posts as $post){
					$this->createNewPost($post);
				}
			}

			$this->flashMessage('Import was successful.');
		}

		$this->redirect('this');
	}

	private function createNewPost(array $postData)
	{

		if(isset($postData['images'][1]) and count($postData['images']) and $this->downloadImages){

			foreach($postData['images'][1] as $imageUrl){
				$imageName = $this->getImageName($imageUrl);
				$file = $this->downloadImage($imageUrl);
				if($file) $this->saveImage($file, $imageName);
				$postData['content'] = str_replace($imageUrl, $this->getUrlOfNewImage($imageName), $postData['content']);
			}
		}

		if(isset($postData['category'])){
			$category = $this->createNewCategory($postData['category']);
		}else{
			$category = $this->createNewCategory('Uncategorized');
		}

		$post = new \Flame\CMS\Models\Posts\Post(
			$this->userFacade->getOne($this->getUser()->getId()),
			$postData['name'],
			Strings::createSlug($postData['name']),
			$postData['content'],
			$category
		);

		if($postData['comment'] == 'closed') $comment = false; else $comment = true;
		if($postData['status'] == 'publish') $status = true; else $status = false;

		$post->setDescription($postData['description'])
			->setCreated($postData['pubDate'])
			->setPublish($status)
			->setComment($comment);

		if(isset($postData['tags']) and count($postData['tags'])){
			foreach($postData['tags'] as $tag){
				$post->setTags($this->createNewTag($tag));
			}
		}

		$this->postFacade->save($post);
	}

	private function createNewCategory($name)
	{
		if($category = $this->categoryFacade->getOneByName($name)) return $category;

		$category = new \Flame\CMS\Models\Categories\Category($name, "", Strings::createSlug($name));
		$this->categoryFacade->save($category);
		return $category;
	}

	private function createNewTag($name)
	{
		if($tag = $this->tagFacade->getOneByName($name)) return $tag;

		$tag = new \Flame\CMS\Models\Tags\Tag($name, Strings::createSlug($name));
		$this->tagFacade->save($tag);
		return $tag;
	}

	private function createNewTags(array $tags)
	{
		$prepared = array();

		if(is_array($tags) and count($tags)){
			foreach($tags as $tag){
				$prepared[] = $this->createNewTag($tag);
			}
		}

		return $prepared;
	}

	private function downloadImageCurl($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$file = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if((int) $code < 400) return $file; else return null;
	}

	private function downloadImage($url)
	{
		if(@$file = file_get_contents($url)) return $file; else return null;
	}

	private function createDirForImages()
	{
		if(!file_exists($this->dirName)){
			return mkdir($this->dirName, 0777, true);
		}
	}

	private function saveImage($image, $name)
	{
		return file_put_contents($this->dirName . DIRECTORY_SEPARATOR . $name, $image);
	}

	private function getImageName($url)
	{
		if(strpos($url, '/') !== false){
			$parts = explode('/', $url);
			return $parts[count($parts) - 1];
		}

		return $url;
	}

	private function getUrlOfNewImage($name)
	{
		return $this->getBaseUrl() . $this->imageStorage['importImageDir'] . '/' . $name;
	}

}
