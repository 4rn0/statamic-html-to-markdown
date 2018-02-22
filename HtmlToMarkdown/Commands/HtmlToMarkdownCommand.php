<?php

namespace Statamic\Addons\HtmlToMarkdown\Commands;

use League\HTMLToMarkdown\HtmlConverter;
use Statamic\Extend\Command;
use Statamic\API\Collection;
use Statamic\API\Entry;
use Statamic\API\Page;

class HtmlToMarkdownCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'html-to-markdown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert HTML to Markdown';

    /**
     * The HtmlConverter instance
     *
     * @var null
     */
    private $converter;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {

        $this->converter = new HtmlConverter();
        $this->converter->getConfig()->setOption('strip_tags', true);

        parent::__construct();

    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $choices = array_merge(['pages'], Collection::handles());
        $choice = $this->choice('Which content type would you like to convert?', $choices);
        $data = ($choice === 'pages') ? Page::all() : Entry::whereCollection($choice);

        $this->output->progressStart( $data->count() );
        
        foreach ($data as $item)
        {

            $markdown = $this->converter->convert( $item->content() );            
            $item->content($markdown)->save();

            $this->output->progressAdvance();

        }

        $this->output->progressFinish();

    }

}