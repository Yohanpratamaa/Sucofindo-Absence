<x-filament-panels::page>
<style>
    /* Custom CSS for Analytics Page */
    .analytics-container {
        padding: 2rem 0;
    }
    
    @media (max-width: 768px) {
        .analytics-container {
            padding: 1.5rem 0;
        }
    }
    
    @media (max-width: 480px) {
        .analytics-container {
            padding: 1rem 0;
        }
    }
    
    .analytics-section {
        margin-bottom: 3rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    
    @media (max-width: 768px) {
        .analytics-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 8px;
        }
    }
    
    @media (max-width: 480px) {
        .analytics-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
        }
    }
    
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
        }
    }
    
    @media (max-width: 480px) {
        .section-header {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }
    }
    
    .section-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    @media (max-width: 480px) {
        .section-header-left {
            gap: 0.75rem;
        }
    }
    
    .icon-container {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        padding: 0.75rem;
        border-radius: 12px;
        color: white;
        font-size: 1.25rem;
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .section-title {
            font-size: 1.25rem;
        }
    }
    
    .section-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    
    @media (max-width: 480px) {
        .section-subtitle {
            font-size: 0.8rem;
        }
    }
    
    .period-badge {
        background: linear-gradient(135deg, #dbeafe, #e0e7ff);
        color: #1e40af;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-weight: 500;
        font-size: 0.875rem;
        border: 1px solid #3b82f6;
    }
    
    /* Metrics Cards */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }
    
    .metric-card {
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .metric-card {
            padding: 1.25rem;
            border-radius: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .metric-card {
            padding: 1rem;
            border-radius: 8px;
        }
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    
    .metric-card-blue {
        background: linear-gradient(135deg, #3b82f6, #1e40af);
    }
    
    .metric-card-green {
        background: linear-gradient(135deg, #10b981, #047857);
    }
    
    .metric-card-orange {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .metric-card-purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    
    .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .metric-icon-bg {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 0.75rem;
        backdrop-filter: blur(10px);
    }
    
    .metric-icon-bg svg {
        width: 2rem;
        height: 2rem;
    }
    
    .metric-emoji {
        font-size: 2rem;
    }
    
    .metric-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .metric-value {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 480px) {
        .metric-value {
            font-size: 1.75rem;
        }
    }
    
    .metric-label {
        font-weight: 500;
        opacity: 0.9;
    }
    
    .metric-footer {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.75rem;
        opacity: 0.8;
    }
    
    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    @media (max-width: 1280px) {
        .charts-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
    }
    
    /* Weekly Chart */
    .chart-container {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    @media (max-width: 768px) {
        .chart-container {
            padding: 1.5rem;
            border-radius: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .chart-container {
            padding: 1rem;
            border-radius: 8px;
        }
    }
    
    .weekly-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .weekly-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .weekly-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }
    
    .week-column {
        text-align: center;
    }
    
    .week-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
    }
    
    .bar-container {
        margin-bottom: 0.75rem;
    }
    
    .bar-bg {
        border-radius: 999px;
        position: relative;
        overflow: hidden;
        border: 2px solid;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .bar-bg-green {
        background: #dcfce7;
        border-color: #bbf7d0;
        height: 24px;
    }
    
    .bar-bg-red {
        background: #fecaca;
        border-color: #fca5a5;
        height: 16px;
    }
    
    .bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: all 0.7s ease-out;
    }
    
    .bar-fill-green {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .bar-fill-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    .bar-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
        font-weight: 700;
        color: #374151;
    }
    
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 2rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .chart-legend {
            gap: 1.5rem;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .chart-legend {
            gap: 1rem;
            font-size: 0.75rem;
            flex-direction: column;
            align-items: center;
        }
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .legend-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .legend-dot-green {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: #047857;
    }
    
    .legend-dot-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: #b91c1c;
    }
    
    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .summary-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
    }
    
    .summary-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .summary-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .summary-card-blue { border-color: #93c5fd; }
    .summary-card-green { border-color: #86efac; }
    .summary-card-red { border-color: #fca5a5; }
    .summary-card-purple { border-color: #c4b5fd; }
    
    .summary-value {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .summary-value-blue { color: #1d4ed8; }
    .summary-value-green { color: #047857; }
    .summary-value-red { color: #b91c1c; }
    .summary-value-purple { color: #7c3aed; }
    
    .summary-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    /* Performance Section */
    .performance-container {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    
    @media (max-width: 768px) {
        .performance-container {
            padding: 1.5rem;
            border-radius: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .performance-container {
            padding: 1rem;
            border-radius: 8px;
        }
    }
    
    .performance-overview {
        margin-bottom: 2rem;
    }
    
    .top-performer {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 2px solid #86efac;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .top-performer {
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .top-performer {
            padding: 1rem;
            border-radius: 8px;
        }
    }
    
    .performer-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .performer-emoji {
        font-size: 2rem;
    }
    
    .performer-title {
        font-size: 1rem;
        font-weight: 700;
        color: #14532d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    
    .performer-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.5rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .performer-details {
        font-size: 1rem;
        font-weight: 600;
        color: #166534;
        background: rgba(255, 255, 255, 0.7);
        padding: 0.5rem 1rem;
        border-radius: 999px;
        display: inline-block;
        border: 1px solid #86efac;
    }
    
    .performer-unavailable {
        color: #6b7280;
        font-style: italic;
        font-size: 1rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 8px;
    }
    
    .performance-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-top: 1rem;
    }
    
    @media (max-width: 768px) {
        .performance-stats-grid {
            gap: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .performance-stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }
    
    .performance-stat-card {
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        border: 2px solid;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .performance-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    
    .performance-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    }
    
    .stat-card-blue {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: #93c5fd;
    }
    
    .stat-card-orange {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fdba74;
    }
    
    .stat-card-orange::before {
        background: linear-gradient(90deg, #f59e0b, #ea580c);
    }
    
    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        color: #1f2937;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .stat-description {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .performance-list {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        margin-top: 2rem;
    }
    
    .performance-list-header {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        padding: 1.5rem 2rem;
        border-bottom: 3px solid #cbd5e1;
        position: relative;
    }
    
    .performance-list-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    }
    
    .performance-list-title {
        font-weight: 800;
        font-size: 1.125rem;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .performance-list-content {
        max-height: 28rem;
        overflow-y: auto;
    }
    
    @media (max-width: 768px) {
        .performance-list-content {
            max-height: 24rem;
        }
    }
    
    @media (max-width: 480px) {
        .performance-list-content {
            max-height: 20rem;
        }
    }
    
    .performance-item {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        position: relative;
    }
    
    @media (max-width: 768px) {
        .performance-item {
            padding: 1.25rem 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .performance-item {
            padding: 1rem;
        }
    }
    
    .performance-item:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transform: translateX(4px);
    }
    
    .performance-item:last-child {
        border-bottom: none;
    }
    
    .performance-item-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    @media (max-width: 768px) {
        .performance-item-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
    
    .employee-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex: 1;
    }
    
    @media (max-width: 768px) {
        .employee-info {
            width: 100%;
            gap: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .employee-info {
            gap: 0.75rem;
        }
    }
    
    .rank-badge {
        flex-shrink: 0;
        position: relative;
    }
    
    .rank-badge-gold, .rank-badge-silver, .rank-badge-bronze, .rank-badge-regular {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .rank-badge-gold, .rank-badge-silver, .rank-badge-bronze, .rank-badge-regular {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 0.875rem;
        }
    }
    
    @media (max-width: 480px) {
        .rank-badge-gold, .rank-badge-silver, .rank-badge-bronze, .rank-badge-regular {
            width: 2rem;
            height: 2rem;
            font-size: 0.75rem;
        }
    }
    
    .rank-badge-gold, .rank-badge-silver, .rank-badge-bronze, .rank-badge-regular::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border-radius: 50%;
        background: linear-gradient(45deg, rgba(255,255,255,0.3), transparent);
        z-index: 1;
    }
    
    .rank-badge-gold {
        background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
        box-shadow: 0 6px 12px rgba(251, 191, 36, 0.4);
    }
    
    .rank-badge-silver {
        background: linear-gradient(135deg, #e5e7eb, #9ca3af, #6b7280);
        box-shadow: 0 6px 12px rgba(156, 163, 175, 0.4);
    }
    
    .rank-badge-bronze {
        background: linear-gradient(135deg, #fb923c, #ea580c, #c2410c);
        box-shadow: 0 6px 12px rgba(251, 146, 60, 0.4);
    }
    
    .rank-badge-regular {
        background: linear-gradient(135deg, #94a3b8, #64748b, #475569);
        box-shadow: 0 6px 12px rgba(148, 163, 184, 0.4);
    }
    
    .employee-details {
        flex: 1;
        min-width: 0;
    }
    
    .employee-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 1rem;
        margin-bottom: 0.375rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    @media (max-width: 768px) {
        .employee-name {
            font-size: 0.925rem;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }
    }
    
    @media (max-width: 480px) {
        .employee-name {
            font-size: 0.875rem;
        }
    }
    
    .employee-attendance {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    @media (max-width: 480px) {
        .employee-attendance {
            font-size: 0.8rem;
        }
    }
    
    .performance-badge-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        min-width: 80px;
    }
    
    @media (max-width: 768px) {
        .performance-badge-container {
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            min-width: auto;
            width: 100%;
            gap: 0.75rem;
        }
    }
    
    .attendance-rate {
        font-size: 1rem;
        font-weight: 800;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
        color: #052e16;
    }
    
    .performer-rate {
        font-size: 0.875rem;
        color: #166534;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-card {
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 2px solid;
    }
    
    .stat-card-blue {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: #93c5fd;
    }
    
    .stat-card-orange {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fdba74;
    }
    
    .stat-title {
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .stat-title-blue { color: #1e3a8a; }
    .stat-title-orange { color: #92400e; }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stat-value-blue { color: #1e40af; }
    .stat-value-orange { color: #b45309; }
    
    .stat-label {
        font-size: 0.75rem;
    }
    
    .stat-label-blue { color: #1e40af; }
    .stat-label-orange { color: #b45309; }
    
    /* Ranking List */
    .ranking-header {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #d1d5db;
    }
    
    .ranking-title {
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    .ranking-list {
        max-height: 384px;
        overflow-y: auto;
    }
    
    .ranking-item {
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    
    .ranking-item:hover {
        background: #f9fafb;
    }
    
    .ranking-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .rank-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .rank-badge-gold {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }
    
    .rank-badge-silver {
        background: linear-gradient(135deg, #9ca3af, #6b7280);
    }
    
    .rank-badge-bronze {
        background: linear-gradient(135deg, #fb923c, #ea580c);
    }
    
    .rank-badge-default {
        background: linear-gradient(135deg, #cbd5e1, #64748b);
    }
    
    .employee-info h4 {
        font-weight: 600;
        color: #111827;
        font-size: 0.875rem;
        margin: 0 0 0.25rem 0;
    }
    
    .employee-info p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }
    
    .performance-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }
    
    .attendance-rate {
        font-size: 0.875rem;
        font-weight: 700;
    }
    
    .rate-excellent { 
        color: #047857;
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    }
    
    .rate-good { 
        color: #1d4ed8;
        border-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
    }
    
    .rate-poor { 
        color: #b91c1c;
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2, #fecaca);
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 999px;
        border: 2px solid;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.25px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .badge-excellent {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #14532d;
        border-color: #22c55e;
    }
    
    .badge-good {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e3a8a;
        border-color: #3b82f6;
    }
    
    .badge-average {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border-color: #f59e0b;
    }
    
    .badge-attention {
        background: linear-gradient(135deg, #fecaca, #fca5a5);
        color: #7f1d1d;
        border-color: #ef4444;
    }
    
    /* Insights Section */
    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .insights-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .insights-grid {
            gap: 1rem;
        }
    }
    
    .insight-card {
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 2px solid;
    }
    
    @media (max-width: 768px) {
        .insight-card {
            padding: 1.5rem;
            border-radius: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .insight-card {
            padding: 1rem;
            border-radius: 8px;
        }
    }
    
    .insight-card-blue {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: #93c5fd;
    }
    
    .insight-card-orange {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fdba74;
    }
    
    .insight-card-green {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-color: #86efac;
    }
    
    .insight-header {
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.125rem;
    }
    
    .insight-header-blue { color: #1e3a8a; }
    .insight-header-orange { color: #92400e; }
    .insight-header-green { color: #14532d; }
    
    .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        background: rgba(255, 255, 255, 0.5);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .insight-icon {
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .insight-text {
        font-weight: 500;
    }
    
    .insight-text-blue { color: #1e40af; }
    .insight-text-orange { color: #b45309; }
    .insight-text-green { color: #166534; }
    .insight-text-green { color: #166534; }
    
    /* Additional responsive fixes */
    @media (max-width: 1024px) {
        .analytics-container {
            padding: 1.5rem 0;
        }
        
        .performance-list-header {
            padding: 1.25rem 1.5rem;
        }
        
        .performance-list-title {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 640px) {
        .period-badge {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }
        
        .metric-emoji {
            font-size: 1.5rem;
        }
        
        .metric-icon-bg svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        
        .performer-emoji {
            font-size: 1.5rem;
        }
        
        .performer-name {
            font-size: 1.25rem;
        }
        
        .performer-details {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
        }
        
        .stat-value {
            font-size: 1.75rem;
        }
        
        .attendance-rate {
            font-size: 0.875rem;
            padding: 0.2rem 0.5rem;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    }
    
    /* Prevent horizontal scroll */
    * {
        box-sizing: border-box;
    }
    
    .analytics-container {
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
    }
    
    /* Improve touch targets on mobile */
    @media (max-width: 768px) {
        .performance-item {
            min-height: 60px;
        }
        
        .rank-badge {
            flex-shrink: 0;
        }
        
        .employee-details {
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }
    }
</style>

    @php
        $currentMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');
        
        // Get team members
        $teamMembers = \App\Models\Pegawai::where('role_user', 'employee')->where('status', 'active')->get();
        
        // Current month stats
        $currentMonthAttendance = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->count();
        $currentMonthLate = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")->whereRaw("TIME(check_in) > '08:00:00'")->count();
        
        // Last month stats for comparison
        $lastMonthAttendance = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$lastMonth}'")->count();
        $lastMonthLate = \App\Models\Attendance::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$lastMonth}'")->whereRaw("TIME(check_in) > '08:00:00'")->count();
        
        // Calculate rates
        $attendanceRate = $teamMembers->count() > 0 ? round(($currentMonthAttendance / ($teamMembers->count() * now()->day)) * 100, 1) : 0;
        $punctualityRate = $currentMonthAttendance > 0 ? round((($currentMonthAttendance - $currentMonthLate) / $currentMonthAttendance) * 100, 1) : 0;
        
        // Individual employee performance
        $employeePerformance = $teamMembers->map(function($employee) use ($currentMonth) {
            $attendances = \App\Models\Attendance::where('user_id', $employee->id)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '{$currentMonth}'")
                ->get();
            
            $lateCount = $attendances->where('check_in', '>', '08:00:00')->count();
            $attendanceCount = $attendances->count();
            $workDays = now()->day;
            
            return [
                'id' => $employee->id,
                'name' => $employee->nama,
                'attendance_count' => $attendanceCount,
                'late_count' => $lateCount,
                'attendance_rate' => $workDays > 0 ? round(($attendanceCount / $workDays) * 100, 1) : 0,
                'punctuality_rate' => $attendanceCount > 0 ? round((($attendanceCount - $lateCount) / $attendanceCount) * 100, 1) : 0,
                'avg_work_hours' => $attendances->where('check_out', '!=', null)->avg(function($attendance) {
                    if ($attendance->check_in && $attendance->check_out) {
                        return \Carbon\Carbon::parse($attendance->check_in)->diffInHours(\Carbon\Carbon::parse($attendance->check_out));
                    }
                    return 0;
                }) ?: 0
            ];
        })->sortByDesc('attendance_rate');
        
        // Weekly trend data (last 4 weeks)
        $weeklyTrends = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weekAttendance = \App\Models\Attendance::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $weekLate = \App\Models\Attendance::whereBetween('created_at', [$weekStart, $weekEnd])
                ->whereRaw("TIME(check_in) > '08:00:00'")->count();
            
            $weeklyTrends[] = [
                'week' => 'Minggu ' . (4 - $i),
                'attendance' => $weekAttendance,
                'late' => $weekLate,
                'punctual' => $weekAttendance - $weekLate
            ];
        }
    @endphp

    <!-- Analytics Overview -->
    <div class="analytics-container">
        <!-- Key Metrics -->
        <div class="analytics-section">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="icon-container">
                        <span>📊</span>
                    </div>
                    <div>
                        <h2 class="section-title">Overview Analisis Absensi</h2>
                        <p class="section-subtitle">Ringkasan performa tim bulan ini</p>
                    </div>
                </div>
                <span class="period-badge">{{ now()->format('F Y') }}</span>
            </div>
            
            <div class="metrics-grid">
                <!-- Total Team Members -->
                <div class="metric-card metric-card-blue">
                    <div class="metric-header">
                        <div class="metric-icon-bg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="metric-emoji">👥</span>
                    </div>
                    <div class="metric-value">{{ $teamMembers->count() }}</div>
                    <div class="metric-label">Total Anggota Tim</div>
                    <div class="metric-footer">
                        <span>Karyawan aktif</span>
                    </div>
                </div>

                <!-- Attendance Rate -->
                <div class="metric-card metric-card-green">
                    <div class="metric-header">
                        <div class="metric-icon-bg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="metric-emoji">✅</span>
                    </div>
                    <div class="metric-value">{{ $attendanceRate }}%</div>
                    <div class="metric-label">Tingkat Kehadiran</div>
                    @if($lastMonthAttendance > 0)
                        @php $change = $currentMonthAttendance - $lastMonthAttendance; @endphp
                        <div class="metric-footer">
                            @if($change >= 0)
                                <span>+{{ $change }} dari bulan lalu</span>
                            @else
                                <span>{{ $change }} dari bulan lalu</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Punctuality Rate -->
                <div class="metric-card metric-card-orange">
                    <div class="metric-header">
                        <div class="metric-icon-bg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="metric-emoji">⏰</span>
                    </div>
                    <div class="metric-value">{{ $punctualityRate }}%</div>
                    <div class="metric-label">Ketepatan Waktu</div>
                    <div class="metric-footer">
                        <span>{{ $currentMonthLate }} dari {{ $currentMonthAttendance }} terlambat</span>
                    </div>
                </div>

                <!-- Total Attendance -->
                <div class="metric-card metric-card-purple">
                    <div class="metric-header">
                        <div class="metric-icon-bg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="metric-emoji">📈</span>
                    </div>
                    <div class="metric-value">{{ $currentMonthAttendance }}</div>
                    <div class="metric-label">Total Absensi</div>
                    <div class="metric-footer">
                        <span>Bulan {{ now()->format('F') }}</span>
                    </div>
                </div>
            </div>
        </div>

    <div class="charts-grid">
        <!-- Weekly Trends Chart -->
        <div class="analytics-section">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="icon-container">
                        📈
                    </div>
                    <div>
                        <h2 class="section-title">Trend Mingguan</h2>
                        <p class="section-subtitle">Analisis 4 minggu terakhir</p>
                    </div>
                </div>
            </div>
            
            <div>
                <!-- Chart Area -->
                <div class="chart-container">
                    <div class="weekly-grid">
                        @foreach($weeklyTrends as $week)
                            <div class="week-column">
                                <div class="week-label">{{ $week['week'] }}</div>
                                <div class="bar-container">
                                    <!-- Attendance Bar -->
                                    <div class="bar-bg bar-bg-green">
                                        <div class="bar-fill bar-fill-green"
                                             style="width: {{ $week['attendance'] > 0 ? min(($week['attendance'] / max(array_column($weeklyTrends, 'attendance'))) * 100, 100) : 0 }}%">
                                        </div>
                                        <div class="bar-text">
                                            {{ $week['attendance'] }}
                                        </div>
                                    </div>
                                    <!-- Late Bar -->
                                    <div class="bar-bg bar-bg-red">
                                        <div class="bar-fill bar-fill-red"
                                             style="width: {{ $week['late'] > 0 && max(array_column($weeklyTrends, 'late')) > 0 ? min(($week['late'] / max(array_column($weeklyTrends, 'late'))) * 100, 100) : 0 }}%">
                                        </div>
                                        <div class="bar-text">
                                            {{ $week['late'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Legend -->
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot legend-dot-green"></div>
                            <span>Total Hadir</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot legend-dot-red"></div>
                            <span>Terlambat</span>
                        </div>
                    </div>
                </div>
                
                <!-- Weekly Summary -->
                <div class="summary-grid">
                    @php
                        $totalWeeklyAttendance = array_sum(array_column($weeklyTrends, 'attendance'));
                        $totalWeeklyLate = array_sum(array_column($weeklyTrends, 'late'));
                        $avgWeeklyAttendance = count($weeklyTrends) > 0 ? round($totalWeeklyAttendance / count($weeklyTrends), 1) : 0;
                        $weeklyPunctualityRate = $totalWeeklyAttendance > 0 ? round((($totalWeeklyAttendance - $totalWeeklyLate) / $totalWeeklyAttendance) * 100, 1) : 0;
                    @endphp
                    
                    <div class="summary-card summary-card-blue">
                        <div class="summary-value summary-value-blue">{{ $totalWeeklyAttendance }}</div>
                        <div class="summary-label">Total 4 Minggu</div>
                    </div>
                    <div class="summary-card summary-card-green">
                        <div class="summary-value summary-value-green">{{ $avgWeeklyAttendance }}</div>
                        <div class="summary-label">Rata-rata/Minggu</div>
                    </div>
                    <div class="summary-card summary-card-red">
                        <div class="summary-value summary-value-red">{{ $totalWeeklyLate }}</div>
                        <div class="summary-label">Total Terlambat</div>
                    </div>
                    <div class="summary-card summary-card-purple">
                        <div class="summary-value summary-value-purple">{{ $weeklyPunctualityRate }}%</div>
                        <div class="summary-label">Ketepatan Waktu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Performance Analysis -->
        <div class="performance-container">
            <div class="section-header">
                <div class="section-header-left">
                    <div class="icon-container">
                        👤
                    </div>
                    <div>
                        <h2 class="section-title">Performa Individual</h2>
                        <p class="section-subtitle">Analisis performa karyawan terbaik</p>
                    </div>
                </div>
            </div>
            
            <div class="performance-overview">
                @php
                    $topPerformer = $employeePerformance->first();
                    $needsAttention = $employeePerformance->where('attendance_rate', '<', 80)->count();
                    $excellentPerformers = $employeePerformance->where('attendance_rate', '>=', 95)->count();
                @endphp
                
                <div class="top-performer">
                    <div class="performer-header">
                        <span class="performer-emoji">🏆</span>
                        <div class="performer-title">Top Performer</div>
                    </div>
                    @if($topPerformer)
                        <div class="performer-name">{{ $topPerformer['name'] }}</div>
                        <div class="performer-details">{{ $topPerformer['attendance_rate'] }}% tingkat kehadiran</div>
                    @else
                        <div class="performer-unavailable">Data tidak tersedia</div>
                    @endif
                </div>
                
                <div class="performance-stats-grid">
                    <div class="performance-stat-card stat-card-blue">
                        <div class="stat-label">⭐ Excellent</div>
                        <div class="stat-value">{{ $excellentPerformers }}</div>
                        <div class="stat-description">≥95% kehadiran</div>
                    </div>
                    
                    <div class="performance-stat-card stat-card-orange">
                        <div class="stat-label">⚠️ Perhatian</div>
                        <div class="stat-value">{{ $needsAttention }}</div>
                        <div class="stat-description"><80% kehadiran</div>
                    </div>
                </div>
            </div>
            
            <!-- Employee Ranking List -->
            <div class="performance-list">
                <div class="performance-list-header">
                    <h4 class="performance-list-title">
                        <span>🏅</span>
                        Ranking Performa Karyawan
                    </h4>
                </div>
                
                <div class="performance-list-content">
                    @foreach($employeePerformance->take(10) as $index => $employee)
                        <div class="performance-item">
                            <div class="performance-item-content">
                                <div class="employee-info">
                                    <!-- Rank Badge -->
                                    <div class="rank-badge">
                                        @if($index == 0)
                                            <div class="rank-badge-gold">
                                                🥇
                                            </div>
                                        @elseif($index == 1)
                                            <div class="rank-badge-silver">
                                                🥈
                                            </div>
                                        @elseif($index == 2)
                                            <div class="rank-badge-bronze">
                                                🥉
                                            </div>
                                        @else
                                            <div class="rank-badge-regular">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Employee Details -->
                                    <div class="employee-details">
                                        <div class="employee-name">{{ $employee['name'] }}</div>
                                        <div class="employee-attendance">{{ $employee['attendance_count'] }} dari {{ now()->day }} hari kerja</div>
                                    </div>
                                </div>
                                
                                <!-- Performance Metrics -->
                                <div class="performance-badge-container">
                                    <div class="attendance-rate {{ $employee['attendance_rate'] >= 95 ? 'rate-excellent' : ($employee['attendance_rate'] >= 80 ? 'rate-good' : 'rate-poor') }}">
                                        {{ $employee['attendance_rate'] }}%
                                    </div>
                                    @if($employee['attendance_rate'] >= 95 && $employee['punctuality_rate'] >= 95)
                                        <span class="status-badge badge-excellent">Excellent</span>
                                    @elseif($employee['attendance_rate'] >= 85)
                                        <span class="status-badge badge-good">Good</span>
                                    @elseif($employee['attendance_rate'] >= 70)
                                        <span class="status-badge badge-average">Average</span>
                                    @else
                                        <span class="status-badge badge-attention">Attention</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Insights & Recommendations -->
    <div class="analytics-section">
        <div class="section-header">
            <div class="section-header-left">
                <div class="icon-container">
                    💡
                </div>
                <div>
                    <h2 class="section-title">Insights & Rekomendasi</h2>
                    <p class="section-subtitle">Analisis otomatis dan saran perbaikan</p>
                </div>
            </div>
        </div>
        
        <div class="insights-grid">
            <!-- Attendance Insights -->
            <div class="insight-card insight-card-blue">
                <h3 class="insight-header insight-header-blue">
                    <span>📊</span>
                    Analisis Kehadiran
                </h3>
                <div>
                    @if($attendanceRate >= 90)
                        <div class="insight-item">
                            <span class="insight-icon">✅</span>
                            <span class="insight-text insight-text-blue">Tingkat kehadiran excellent ({{ $attendanceRate }}%)</span>
                        </div>
                    @elseif($attendanceRate >= 75)
                        <div class="insight-item">
                            <span class="insight-icon">⚠️</span>
                            <span class="insight-text insight-text-blue">Tingkat kehadiran cukup baik ({{ $attendanceRate }}%)</span>
                        </div>
                    @else
                        <div class="insight-item">
                            <span class="insight-icon">❌</span>
                            <span class="insight-text insight-text-blue">Tingkat kehadiran perlu ditingkatkan ({{ $attendanceRate }}%)</span>
                        </div>
                    @endif
                    
                    @if($excellentPerformers > $teamMembers->count() * 0.7)
                        <div class="insight-item">
                            <span class="insight-icon">🏆</span>
                            <span class="insight-text insight-text-blue">Mayoritas tim ({{ $excellentPerformers }}/{{ $teamMembers->count() }}) memiliki performa excellent</span>
                        </div>
                    @endif
                    
                    @if($needsAttention > 0)
                        <div class="insight-item">
                            <span class="insight-icon">🎯</span>
                            <span class="insight-text insight-text-blue">{{ $needsAttention }} karyawan membutuhkan perhatian khusus</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Punctuality Insights -->
            <div class="insight-card insight-card-orange">
                <h3 class="insight-header insight-header-orange">
                    <span>⏰</span>
                    Analisis Ketepatan Waktu
                </h3>
                <div>
                    @if($punctualityRate >= 90)
                        <div class="insight-item">
                            <span class="insight-icon">✅</span>
                            <span class="insight-text insight-text-orange">Ketepatan waktu sangat baik ({{ $punctualityRate }}%)</span>
                        </div>
                    @elseif($punctualityRate >= 75)
                        <div class="insight-item">
                            <span class="insight-icon">⚠️</span>
                            <span class="insight-text insight-text-orange">Ketepatan waktu cukup ({{ $punctualityRate }}%)</span>
                        </div>
                    @else
                        <div class="insight-item">
                            <span class="insight-icon">❌</span>
                            <span class="insight-text insight-text-orange">Ketepatan waktu perlu diperbaiki ({{ $punctualityRate }}%)</span>
                        </div>
                    @endif
                    
                    <div class="insight-item">
                        <span class="insight-icon">📈</span>
                        <span class="insight-text insight-text-orange">{{ $currentMonthLate }} dari {{ $currentMonthAttendance }} kehadiran terlambat</span>
                    </div>
                    
                    @if($currentMonthLate > $lastMonthLate)
                        <div class="insight-item">
                            <span class="insight-icon">⬆️</span>
                            <span class="insight-text insight-text-orange">Peningkatan keterlambatan dari bulan lalu</span>
                        </div>
                    @elseif($currentMonthLate < $lastMonthLate)
                        <div class="insight-item">
                            <span class="insight-icon">⬇️</span>
                            <span class="insight-text insight-text-orange">Penurunan keterlambatan dari bulan lalu</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Recommendations -->
            <div class="insight-card insight-card-green">
                <h3 class="insight-header insight-header-green">
                    <span>🎯</span>
                    Rekomendasi Tindakan
                </h3>
                <div>
                    @if($needsAttention > 0)
                        <div class="insight-item">
                            <span class="insight-icon">👥</span>
                            <span class="insight-text insight-text-green">Lakukan one-on-one meeting dengan {{ $needsAttention }} karyawan yang perlu perhatian</span>
                        </div>
                    @endif
                    
                    @if($currentMonthLate > $teamMembers->count() * 0.3)
                        <div class="insight-item">
                            <span class="insight-icon">📢</span>
                            <span class="insight-text insight-text-green">Adakan briefing tentang pentingnya ketepatan waktu</span>
                        </div>
                    @endif
                    
                    @if($excellentPerformers > 0)
                        <div class="insight-item">
                            <span class="insight-icon">🏆</span>
                            <span class="insight-text insight-text-green">Berikan apresiasi kepada {{ $excellentPerformers }} top performer</span>
                        </div>
                    @endif
                    
                    <div class="insight-item">
                        <span class="insight-icon">📊</span>
                        <span class="insight-text insight-text-green">Gunakan data ini untuk evaluasi kinerja bulanan</span>
                    </div>
                    
                    <div class="insight-item">
                        <span class="insight-icon">📋</span>
                        <span class="insight-text insight-text-green">Export detail per karyawan untuk review mendalam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
