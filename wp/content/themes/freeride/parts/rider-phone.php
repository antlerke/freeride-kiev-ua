<?php
/**
 * Телефон із екраном Rider Card на головній.
 *
 * Це ілюстрація майбутнього застосунку, а не вміст сайту, тому вона
 * лишається розміткою й не виноситься в поля: редагувати тут нема чого,
 * а зламати макет пустим полем — легко. Коли застосунок запрацює,
 * цей блок замінить справжній знімок екрана.
 */

declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="phone">
  <div class="phone-body" role="img" aria-label="Екран застосунку Rider Card: прогрес 128 зі 150 балів, 85 відсотків">
    <span class="phone-btn phone-btn-a" aria-hidden="true"></span>
    <span class="phone-btn phone-btn-b" aria-hidden="true"></span>
    <span class="phone-btn phone-btn-c" aria-hidden="true"></span>
    <div class="phone-screen">
      <div class="phone-bar" aria-hidden="true">
        <span>11:41</span>
        <svg width="54" height="12" viewBox="0 0 54 12" fill="currentColor">
          <rect x="0"  y="7.5" width="3" height="4.5" rx="1"/>
          <rect x="4.6" y="5.5" width="3" height="6.5" rx="1"/>
          <rect x="9.2" y="3.2" width="3" height="8.8" rx="1"/>
          <rect x="13.8" y="1" width="3" height="11" rx="1"/>
          <path d="M21.4 4.1a8.4 8.4 0 0 1 10.2 0l-1.3 1.6a6.3 6.3 0 0 0-7.6 0l-1.3-1.6Zm2.3 2.9a4.8 4.8 0 0 1 5.6 0l-2.8 3.5-2.8-3.5Z"/>
          <rect x="37" y="1.6" width="14.4" height="8.8" rx="2.6" fill="none" stroke="currentColor" stroke-width="1.1"/>
          <rect x="38.6" y="3.2" width="9.6" height="5.6" rx="1.4"/>
          <path d="M52.6 4.6v4.2a2.2 2.2 0 0 0 0-4.2Z"/>
        </svg>
      </div>
      <div class="phone-island" aria-hidden="true"></div>
      <div class="phone-app">
        <b>FreeRide®</b>
        <span>Rider Card</span>
        <svg class="phone-ring" viewBox="0 0 150 150" aria-hidden="true">
          <circle cx="75" cy="75" r="60" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="11"/>
          <circle cx="75" cy="75" r="60" fill="none" stroke="#69BE28" stroke-width="11" stroke-linecap="round"
                  stroke-dasharray="377" stroke-dashoffset="56.6" transform="rotate(-90 75 75)"/>
          <text x="75" y="62" text-anchor="middle" fill="#D8DADB" font-size="13" font-family="Manrope, sans-serif">Прогрес</text>
          <text x="75" y="88" text-anchor="middle" fill="#fff" font-size="22" font-weight="800" font-style="italic" font-family="Montserrat, sans-serif">128 / 150</text>
          <text x="75" y="106" text-anchor="middle" fill="#D8DADB" font-size="13" font-family="Manrope, sans-serif">балів</text>
          <text x="75" y="124" text-anchor="middle" fill="#69BE28" font-size="15" font-weight="700" font-family="Manrope, sans-serif">85%</text>
        </svg>
        <span class="phone-hint">Наступна ціль: міський рух</span>
      </div>
      <div class="phone-home" aria-hidden="true"></div>
    </div>
  </div>
</div>
