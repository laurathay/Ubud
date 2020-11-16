<?php
/**
 * Single Event Meta (Venue) Template
 *
 * Copied from: the-events-calendar/src/functions/template-tags/venue.php
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( ! tribe_get_venue_id() ) {
	return;
}

?>

<div class="entry-event">
	<div>
		<div class="event-schedule">
			<?php echo tribe_events_event_schedule_details( null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="event-venue">
			<?php echo tribe_get_venue(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php if ( tribe_address_exists() ) : ?>
			<div class="event-venue-location">
				<address class="events-address">
					<?php echo tribe_get_full_address(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</address>
			</div>
		<?php endif; ?>
	</div>
</div>
