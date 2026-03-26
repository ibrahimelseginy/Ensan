import { Component, Input, Output, EventEmitter, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Campaign } from '../../models/campaign.model';
import { CampaignCardComponent } from '../campaign-card/campaign-card.component';

@Component({
    selector: 'app-campaign-grid',
    standalone: true,
    imports: [CommonModule, CampaignCardComponent],
    templateUrl: './campaign-grid.component.html',
    styleUrls: ['./campaign-grid.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class CampaignGridComponent {
    @Input() campaigns: Campaign[] = [];
    @Output() campaignClick = new EventEmitter<Campaign>();

    trackByCampaignId(index: number, campaign: Campaign): number {
        return campaign.id;
    }

    onCardAction(campaign: Campaign): void {
        this.campaignClick.emit(campaign);
    }
}
