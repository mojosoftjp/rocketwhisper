/**
 * RocketWhisper - メインJavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // ナビゲーション
    // ========================================
    const navbar = document.querySelector('.navbar');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    // スクロール時のナビゲーション背景変更
    function handleScroll() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll(); // 初期状態をチェック

    // モバイルメニュートグル
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }

    // メニューリンクをクリックしたらメニューを閉じる
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
        });
    });

    // ========================================
    // スムーススクロール
    // ========================================
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const offsetTop = target.offsetTop - 80; // ナビゲーションの高さを考慮
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ========================================
    // スクロール連動アニメーション (Intersection Observer)
    // ========================================
    const fadeElements = document.querySelectorAll('.fade-in');

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const fadeObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // 一度表示したら監視を解除（パフォーマンス向上）
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    fadeElements.forEach(el => {
        fadeObserver.observe(el);
    });

    // ========================================
    // FAQアコーディオン
    // ========================================
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function() {
                // 他のアイテムを閉じる
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                // クリックしたアイテムをトグル
                item.classList.toggle('active');
            });
        }
    });

    // ========================================
    // ヒーローセクションのパララックス効果
    // ========================================
    const heroContent = document.querySelector('.hero-content');
    const heroIcon = document.querySelector('.hero-icon');

    function handleParallax() {
        const scrollY = window.scrollY;
        const heroHeight = document.querySelector('.hero')?.offsetHeight || 0;

        if (scrollY < heroHeight && heroContent) {
            const opacity = 1 - (scrollY / heroHeight) * 1.5;
            const translateY = scrollY * 0.3;
            heroContent.style.opacity = Math.max(0, opacity);
            heroContent.style.transform = `translateY(${translateY}px)`;
        }
    }

    window.addEventListener('scroll', handleParallax);

    // ========================================
    // 統計カウントアップアニメーション
    // ========================================
    const statNumbers = document.querySelectorAll('.stat-number');
    let statsAnimated = false;

    function animateStats() {
        if (statsAnimated) return;

        const heroStats = document.querySelector('.hero-stats');
        if (!heroStats) return;

        const rect = heroStats.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
            statsAnimated = true;
            statNumbers.forEach(stat => {
                const finalValue = stat.textContent;
                const isPercentage = finalValue.includes('%');
                const numericValue = parseInt(finalValue);

                if (!isNaN(numericValue)) {
                    let current = 0;
                    const increment = numericValue / 50;
                    const duration = 1500;
                    const stepTime = duration / 50;

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= numericValue) {
                            current = numericValue;
                            clearInterval(timer);
                        }
                        stat.textContent = Math.floor(current) + (isPercentage ? '%' : '');
                    }, stepTime);
                }
            });
        }
    }

    window.addEventListener('scroll', animateStats);
    animateStats(); // 初期状態をチェック

    // ========================================
    // ダークモード（システム設定に連動）
    // ========================================
    // 現在はダークモードがデフォルトなので、将来的にライトモード対応する場合に使用
    /*
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    function updateTheme(e) {
        document.body.classList.toggle('light-mode', !e.matches);
    }
    prefersDark.addListener(updateTheme);
    updateTheme(prefersDark);
    */

    // ========================================
    // 画像の遅延読み込み
    // ========================================
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px'
    });

    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });

    // ========================================
    // コピーボタン（ヘルプページ用）
    // ========================================
    const copyButtons = document.querySelectorAll('.copy-btn');
    copyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const target = document.getElementById(targetId);
            if (target) {
                navigator.clipboard.writeText(target.textContent).then(() => {
                    const originalText = this.textContent;
                    this.textContent = 'コピーしました!';
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 2000);
                });
            }
        });
    });

    // ========================================
    // ページトップに戻るボタン
    // ========================================
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.className = 'scroll-top-btn';
    scrollTopBtn.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="18,15 12,9 6,15"></polyline>
        </svg>
    `;
    scrollTopBtn.setAttribute('aria-label', 'ページトップに戻る');
    document.body.appendChild(scrollTopBtn);

    // スタイルを追加
    const style = document.createElement('style');
    style.textContent = `
        .scroll-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(74, 144, 217, 0.4);
        }
        .scroll-top-btn.visible {
            opacity: 1;
            visibility: visible;
        }
        .scroll-top-btn:hover {
            background: var(--primary-light);
            transform: translateY(-3px);
        }
        .scroll-top-btn svg {
            width: 24px;
            height: 24px;
            color: white;
        }
    `;
    document.head.appendChild(style);

    function handleScrollTopBtn() {
        if (window.scrollY > 500) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    }

    window.addEventListener('scroll', handleScrollTopBtn);

    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // ========================================
    // タイピングエフェクト（オプション）
    // ========================================
    function typeWriter(element, text, speed = 50) {
        let i = 0;
        element.textContent = '';
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }

    // 必要に応じて特定の要素に適用
    // const typeTarget = document.querySelector('.type-effect');
    // if (typeTarget) {
    //     typeWriter(typeTarget, typeTarget.dataset.text);
    // }

    console.log('RocketWhisper website loaded successfully!');
});
