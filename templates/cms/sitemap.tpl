<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
{foreach from=$links item=item}
	<url>
		<loc>{$item.url}</loc>
{if $item.lastmod}
		<lastmod>{$item.lastmod}</lastmod>
{/if}
{if $item.changefreq}
		<changefreq>{$item.changefreq}</changefreq>
{/if}
{if $item.priority}
		<priority>{$item.priority}</priority>
{/if}
{if $item.alternate}{foreach from=$item.alternate item=alternate}
		<xhtml:link rel="alternate" hreflang="{$alternate.language}" href="{$alternate.url}"/>
{/foreach}{/if}
	</url>
{/foreach}</urlset>