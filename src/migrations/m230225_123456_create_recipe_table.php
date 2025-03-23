<?php
use yii\db\Migration;

class m230225_123456_create_recipe_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 COMMENT="食谱表"';
        }

        $this->createTable('{{%recipe}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'title' => $this->string(200)->notNull()->defaultValue('')->comment('食谱(文章)标题'),
            'cover_img' => $this->string(255)->notNull()->defaultValue('')->comment('封面图片'),
            'type' => $this->smallInteger(3)->defaultValue(null)->comment('类型'),
            'recommend' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1：不推荐 2：推荐'),
            'recipe_price' => $this->decimal(10, 2)->notNull()->defaultValue(0)->comment('价格'),
            'detail' => $this->text()->notNull()->comment('详细内容'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'collect_num' => $this->integer(11)->notNull()->defaultValue(0)->comment('收藏数'),
            'like_num' => $this->integer(11)->notNull()->defaultValue(0)->comment('点赞数'),
            'recipe_status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1:审核中 2：审核通过  3：审核未通过  4：被举报'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);

        $this->execute("

INSERT INTO `recipe` ( `title`, `cover_img`, `type`, `recommend`, `detail`, `user_id`, `collect_num`, `like_num`, `created_at`, `updated_at`) VALUES ('The Fundamentals of Stock Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202443_pzskgw6oke.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829084&Signature=qL3159CP3xywEa1NuAabNSqra80%3D','1','2','Investing in stocks involves purchasing shares of a company, thereby owning a portion of that company. Investors can profit from stocks through price appreciation and dividends. A well-diversified stock portfolio helps mitigate risks. Researching companies, analyzing financial statements, and staying informed about market trends are crucial for successful stock investments. Long-term investing in stable companies often yields better returns compared to short-term speculation.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Understanding Dividend Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310203034_e1i9rtpnfa.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829434&Signature=5qlZxXa2O8vOS5j1Fkldp1NJaPA%3D','1','2','Dividend investing focuses on purchasing stocks that pay regular dividends, providing investors with a steady income stream. Companies that offer dividends are usually financially stable with strong earnings. Reinvesting dividends can accelerate wealth accumulation through the power of compounding. Investors should evaluate dividend yield, payout ratio, and company financial health to make informed decisions. A well-structured dividend portfolio can be a reliable source of passive income.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
, ('The Role of Stock Market Indices','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202543_5oxhrj6are.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829143&Signature=3JG7l2w6qh3U0fweWYV1X0oANCU%3D','1','2','Stock market indices, such as the S&P 500 and Dow Jones Industrial Average, represent the performance of a specific group of stocks. They provide insight into overall market trends and economic conditions. Investors use indices to compare their portfolio performance and make informed investment decisions. Index funds and ETFs track these indices, offering low-cost diversification. Understanding market indices helps investors assess risks and opportunities.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('An Introduction to Bond Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202624_yn0oe5rqer.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829184&Signature=YCygQ7Q4xXhrL6aFa0hDpq5nsik%3D','2','2','Bonds are debt securities issued by governments, municipalities, or corporations to raise capital. Investors who purchase bonds essentially lend money in exchange for periodic interest payments and the return of principal at maturity. Bonds are generally less volatile than stocks and provide stable income. However, bond prices fluctuate based on interest rates and credit ratings. Understanding yield, duration, and issuer creditworthiness is key to successful bond investing.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('The Impact of Interest Rates on Bonds','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202642_13032mjnzr.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829202&Signature=QbhFfbcxbirmzuLA%2FsJzbRDhBdU%3D','2','2','Interest rates have an inverse relationship with bond prices when interest rates rise, bond prices typically fall, and vice versa. Central banks influence interest rates to manage inflation and economic growth. Investors should monitor rate trends to adjust their bond holdings accordingly. Short-term bonds are less affected by interest rate changes than long-term bonds. Diversifying across different types of bonds can help mitigate interest rate risk.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Exploring Municipal Bonds as Investment Vehicles','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202657_utyumnbqhs.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829217&Signature=yJPRXbjTWRFHiipJpqGnpGG8Pjo%3D','2','2','Municipal bonds, or munis, are issued by state and local governments to fund public projects like schools, highways, and infrastructure. They offer tax advantages, making them attractive to investors in higher tax brackets. Munis are generally considered low-risk investments but vary based on issuer credit ratings. Investors can choose between general obligation and revenue bonds. Conducting due diligence is essential to selecting high-quality municipal bonds.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('The Benefits of Mutual Fund Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202712_g4u0f7gcel.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829232&Signature=LcwkILJVOuKoGxB%2Fswxx43NYPHA%3D','3','2','Mutual funds pool money from multiple investors to purchase a diversified portfolio of securities, managed by professional fund managers. They offer diversification, liquidity, and professional management. Investors can choose from equity, bond, or balanced funds based on risk tolerance and financial goals. Expense ratios and past performance should be considered before investing. Mutual funds are ideal for beginners seeking diversified investment exposure.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Understanding Exchange-Traded Funds (ETFs)','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202728_jdazsjekgi.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829248&Signature=s8QblsQ5AN9ZcIvy%2B3YV3da7b2s%3D','3','2','Exchange-Traded Funds (ETFs) are investment funds traded on stock exchanges, similar to stocks. They offer diversification, lower expense ratios, and flexibility in trading. ETFs track indices, commodities, or specific sectors, allowing investors to gain exposure to various markets. Unlike mutual funds, ETFs can be bought and sold throughout the trading day. Investors should evaluate ETF liquidity, underlying assets, and expense ratios before investing.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('The Importance of Expense Ratios in Fund Selection','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202748_pvwey4smu7.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829268&Signature=RtZXkvDRMlwHtkhiLtX%2BDxl8LFQ%3D','3','2','Expense ratios represent the annual fees that funds charge their shareholders, expressed as a percentage of the fund’s average assets. Lower expense ratios result in higher net returns for investors. Actively managed funds typically have higher fees than passive index funds. Comparing expense ratios across similar funds helps investors maximize returns. Selecting funds with low costs and strong performance can significantly impact long-term investment success.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('The Advantages of Real Estate Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202811_6zwlkf1wd7.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829291&Signature=tFbHNLG1IxIDFuc4thrBDsIUPb4%3D','4','2','Real estate investing involves purchasing properties to generate income, appreciation, or both. Rental properties provide steady cash flow, while property , tend to appreciate over time. Investors can also explore real estate investment trusts (REITs) for diversified exposure. Location, property condition, and market trends play a crucial role in success. Proper research and financial planning are essential for maximizing returns and minimizing risks.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Strategies for Successful Real Estate Investments','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202831_hyu8zu029z.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829311&Signature=4mx%2BOOnq6VO2VS6m0DIEVXf4lZY%3D','4','2','Successful real estate investing requires careful planning, research, and financial management. Understanding market cycles, financing options, and property valuation is key. Investors should consider long-term appreciation, rental yields, and maintenance costs. Diversifying across different property types and locations reduces risk. Leveraging tax benefits and real estate syndication can further enhance investment returns.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Real Estate vs. Stock Market: A Comparative Analysis','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202845_ox7jzjtfdp.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829325&Signature=sZNfJwAawW5LAbz%2BPV4lOmaWq60%3D','4','2','Investing in real estate and the stock market both offer potential for financial growth, but they differ significantly in risk, returns, and liquidity. Real estate provides tangible assets and rental income, while stocks offer liquidity and higher growth potential. Investors should consider their financial goals, time horizon, and risk tolerance when choosing between these two asset classes. A balanced portfolio may include both real estate and stocks.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Introduction to Cryptocurrency Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202902_7xfsx3po8t.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829342&Signature=kICOna4dgtEJ3ARRBAGobIR%2FyL4%3D','5','2','Cryptocurrency investing has gained popularity in recent years due to the potential for high returns and decentralization. Digital currencies like Bitcoin and Ethereum offer new investment opportunities but come with volatility and security risks. Investors should understand blockchain technology, market trends, and security measures before investing. Diversification and risk management are essential to navigating the rapidly evolving crypto market.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Risks and Rewards of Crypto Investments','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202916_zqvthc6gw5.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829356&Signature=qV2o%2FNhFpGPw%2BNPXBhvH3jFEpcM%3D','5','2','Investing in cryptocurrencies carries unique risks, including volatility, regulatory concerns, and security threats. While some investors have achieved substantial gains, others have faced significant losses. Understanding market trends, investor sentiment, and technical analysis can aid decision-making. Using hardware wallets and secure exchanges is crucial for protecting digital assets. Crypto investments should be a part of a diversified portfolio.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
 , ('Blockchain Technology and Its Impact on Investing','http://id-dc.oss-ap-southeast-5.aliyuncs.com/recipe/20250310202929_vieyllhkhh.png?OSSAccessKeyId=LTAI5tKEYCssvUFMR4oVGY46&Expires=1930829369&Signature=zqekmxt9rSz64X1Fg%2B8i1egybFc%3D','5','2','Blockchain technology is revolutionizing the financial sector by enhancing transparency, security, and efficiency in transactions. Smart contracts and decentralized finance (DeFi) are reshaping traditional banking and investment models. Investors can explore blockchain-based assets, tokenized securities, and decentralized applications. Understanding blockchain fundamentals and emerging trends can provide a strategic advantage in modern investing.','1','0','0','2025-03-10 17:06:38','2025-03-10 17:06:38')
");
    }

    public function down()
    {
        $this->dropTable('{{%recipe}}');
    }
}
