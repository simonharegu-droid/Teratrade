const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');
const leverageInput = document.getElementById('leverageInput');
const leverageValue = document.getElementById('leverageValue');
const priceInput = document.getElementById('priceInput');
const amountInput = document.getElementById('amountInput');
const estimatedCost = document.getElementById('estimatedCost');
const estimatedPnL = document.getElementById('estimatedPnL');
const orderFeedback = document.getElementById('orderFeedback');
const buyButton = document.getElementById('buyButton');
const sellButton = document.getElementById('sellButton');
const orderTypeButtons = document.querySelectorAll('.segmented-control button');
const marketRows = document.getElementById('marketRows');
const bidList = document.getElementById('bidList');
const askList = document.getElementById('askList');
const newsFeed = document.getElementById('newsFeed');
const toast = document.getElementById('toast');
const portfolioBalance = document.getElementById('portfolioBalance');
const currentYear = document.getElementById('currentYear');

const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
});

const sampleMarkets = [
  { symbol: 'BTC/USDT', price: 72654.23, change: 2.35, volume: 495_000_000 },
  { symbol: 'ETH/USDT', price: 3621.58, change: 1.28, volume: 210_500_000 },
  { symbol: 'SOL/USDT', price: 172.45, change: -0.84, volume: 82_200_000 },
  { symbol: 'AVAX/USDT', price: 54.36, change: 0.75, volume: 45_600_000 },
  { symbol: 'XRP/USDT', price: 0.74, change: 3.42, volume: 31_450_000 },
  { symbol: 'DOGE/USDT', price: 0.18, change: -1.12, volume: 25_100_000 },
];

const newsItems = [
  {
    title: 'Bitcoin surges as ETFs record record-breaking inflows',
    summary: 'Institutional appetite continues to grow with $1.3B added in 24h.',
  },
  {
    title: 'Layer-2 ecosystems enter hyper-growth phase',
    summary: 'Scaling solutions unlock new yield opportunities for TerraTrade traders.',
  },
  {
    title: 'TerraTrade adds 12 new perpetual markets',
    summary: 'Trade new high-demand assets with up to 50x leverage and deep liquidity.',
  },
];

const orderbookSnapshot = () => {
  const base = 72650;
  const bids = Array.from({ length: 6 }, (_, idx) => {
    const price = base - idx * 35 - Math.random() * 12;
    const size = (Math.random() * 4 + 0.5).toFixed(2);
    return { price, size };
  });

  const asks = Array.from({ length: 6 }, (_, idx) => {
    const price = base + idx * 35 + Math.random() * 12;
    const size = (Math.random() * 4 + 0.5).toFixed(2);
    return { price, size };
  });

  return { bids, asks };
};

const updateMarketTable = () => {
  marketRows.innerHTML = sampleMarkets
    .map((market) => {
      const changeClass = market.change >= 0 ? 'positive' : 'negative';
      const changeSymbol = market.change >= 0 ? '+' : '';
      return `
        <tr>
          <td>${market.symbol}</td>
          <td>${formatter.format(market.price)}</td>
          <td class="${changeClass}">${changeSymbol}${market.change.toFixed(2)}%</td>
          <td>${formatter.format(market.volume)}</td>
          <td><span class="status">Live</span></td>
        </tr>
      `;
    })
    .join('');
};

const updateOrderbook = () => {
  const { bids, asks } = orderbookSnapshot();
  bidList.innerHTML = bids
    .map((bid) => `<li><span>${bid.price.toFixed(2)}</span><strong>${bid.size} BTC</strong></li>`)
    .join('');
  askList.innerHTML = asks
    .map((ask) => `<li><span>${ask.price.toFixed(2)}</span><strong>${ask.size} BTC</strong></li>`)
    .join('');
};

const renderNews = () => {
  newsFeed.innerHTML = newsItems
    .map(
      (item) => `
        <li>
          <strong>${item.title}</strong>
          <p>${item.summary}</p>
        </li>
      `
    )
    .join('');
};

const showToast = (message, type = 'info') => {
  toast.textContent = message;
  toast.classList.add('show');
  toast.dataset.type = type;
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3800);
};

const updateLeverage = (value) => {
  leverageValue.textContent = `${value}x`;
};

const updateEstimates = () => {
  const price = parseFloat(priceInput.value || '0');
  const amount = parseFloat(amountInput.value || '0');
  const leverage = parseFloat(leverageInput.value);
  const cost = price * amount;
  const pnl = cost * 0.015 * leverage;
  estimatedCost.textContent = formatter.format(cost || 0);
  estimatedPnL.textContent = formatter.format(pnl || 0);
};

const submitOrder = (side) => {
  const pair = document.getElementById('pairSelect').value;
  const type = document.querySelector('.segmented-control button.active')?.dataset.orderType;
  const price = priceInput.value || 'Market';
  const amount = amountInput.value;

  if (!amount) {
    orderFeedback.textContent = 'Enter an amount to trade.';
    orderFeedback.style.color = '#f87171';
    return;
  }

  orderFeedback.style.color = 'var(--success)';
  orderFeedback.textContent = `${side} order placed: ${amount} ${pair} @ ${price} (${type})`;
  showToast(`${side} order confirmed for ${amount} ${pair}`);

  amountInput.value = '';
  if (type === 'Market') {
    priceInput.value = '';
  }
  updateEstimates();
};

const setupChart = () => {
  const ctx = document.getElementById('portfolioChart');
  if (!ctx) return;

  const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
  gradient.addColorStop(0, 'rgba(99, 102, 241, 0.6)');
  gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

  const labels = Array.from({ length: 30 }, (_, idx) => `Day ${idx + 1}`);
  const baseValue = 95000;
  let lastValue = baseValue;
  const data = labels.map(() => {
    const delta = (Math.random() - 0.4) * 2500;
    lastValue = Math.max(baseValue * 0.75, lastValue + delta);
    return lastValue;
  });

  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Portfolio value',
          data,
          tension: 0.35,
          fill: true,
          backgroundColor: gradient,
          borderColor: '#6366f1',
          borderWidth: 2,
          pointRadius: 0,
        },
      ],
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: {
          mode: 'index',
          intersect: false,
          backgroundColor: 'rgba(5, 6, 10, 0.9)',
          borderColor: 'rgba(99, 102, 241, 0.45)',
          borderWidth: 1,
          padding: 12,
        },
      },
      scales: {
        x: {
          ticks: { color: '#94a3b8' },
          grid: { color: 'rgba(148, 163, 184, 0.08)' },
        },
        y: {
          ticks: {
            color: '#94a3b8',
            callback: (value) => formatter.format(value),
          },
          grid: { color: 'rgba(148, 163, 184, 0.08)' },
        },
      },
    },
  });
};

const autoUpdatePortfolio = () => {
  let currentValue = 125430.27;
  setInterval(() => {
    const delta = (Math.random() - 0.45) * 1200;
    currentValue = Math.max(85000, currentValue + delta);
    portfolioBalance.textContent = formatter.format(currentValue);
  }, 5000);
};

if (currentYear) {
  currentYear.textContent = new Date().getFullYear();
}

if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const expanded = navToggle.getAttribute('aria-expanded') === 'true';
    navToggle.setAttribute('aria-expanded', String(!expanded));
    navLinks.classList.toggle('open');
  });
}

if (leverageInput) {
  updateLeverage(leverageInput.value);
  leverageInput.addEventListener('input', (event) => {
    updateLeverage(event.target.value);
    updateEstimates();
  });
}

if (priceInput && amountInput) {
  [priceInput, amountInput].forEach((input) => {
    input.addEventListener('input', updateEstimates);
  });
}

orderTypeButtons.forEach((button) => {
  button.addEventListener('click', () => {
    orderTypeButtons.forEach((btn) => btn.classList.remove('active'));
    button.classList.add('active');
    if (button.dataset.orderType === 'Market') {
      priceInput.value = '';
      priceInput.placeholder = 'Market';
      priceInput.disabled = true;
    } else {
      priceInput.disabled = false;
      priceInput.placeholder = '0.00';
    }
    updateEstimates();
  });
});

if (buyButton) {
  buyButton.addEventListener('click', () => submitOrder('Buy'));
}

if (sellButton) {
  sellButton.addEventListener('click', () => submitOrder('Sell'));
}

updateMarketTable();
updateOrderbook();
renderNews();
setupChart();
autoUpdatePortfolio();
updateEstimates();
setInterval(updateOrderbook, 5000);

const adjustPriceField = () => {
  const activeType = document.querySelector('.segmented-control button.active');
  if (activeType?.dataset.orderType === 'Market') {
    priceInput.disabled = true;
    priceInput.placeholder = 'Market';
  } else {
    priceInput.disabled = false;
    priceInput.placeholder = '0.00';
  }
};

adjustPriceField();
