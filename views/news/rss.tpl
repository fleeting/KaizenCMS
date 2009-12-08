<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>News</title>
		<link>http://{$domain}/</link>
		<description></description>
		<language>en-us</language>
		<pubDate>Tue, 10 Jun 2003 04:00:00 GMT</pubDate>
		<lastBuildDate>{$smarty.now|date_format:'%a, %d %b %Y %T %Z'}</lastBuildDate>
		<atom:link href="http://{$domain}/news/rss/" rel="self" type="application/rss+xml" />
		{foreach from=$aArticles item=aArticle}
		<item>
			<title>{$aArticle.title|clean_html}</title>
			<link>http://{$domain}/news/{$aArticle.id}/{$aArticle.title|special_urlencode}/</link>
			{if !empty($aArticle.short_content)}
			<description>{$aArticle.short_content|clean_html}</description>
			{/if}
			<pubDate>{$aArticle.datetime_show|date_format:'%a, %d %b %Y %T %Z'}</pubDate>
			<guid>http://{$domain}/news/{$aArticle.id}/{$aArticle.title|special_urlencode}/</guid>
		</item>
		{/foreach}
	</channel>
</rss>