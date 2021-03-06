<?php

namespace Frontend\Modules\Blog\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is a widget with recent blog-articles
 */
class RecentArticlesFull extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        // get RSS-link
        $rssTitle = $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE);
        $rssLink = FrontendNavigation::getUrlForBlock('Blog', 'Rss');

        // add RSS-feed
        $this->header->addRssLink($rssTitle, $rssLink);

        // assign comments
        $this->template->assign(
            'widgetBlogRecentArticlesFull',
            FrontendBlogModel::getAll($this->get('fork.settings')->get('Blog', 'recent_articles_full_num_items', 5))
        );
        $this->template->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
    }
}
