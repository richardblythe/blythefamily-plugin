<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="640" style="width:640px;min-width:640px" class="m_-5840917022289077987mlContentTable">
	<tbody><tr>
		<td align="center" style="padding:0px 40px" class="m_-5840917022289077987mlContentOuter">
			<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="560" style="border-radius:2px;border-collapse:separate" class="m_-5840917022289077987mlContentTable">
				<tbody><tr>
					<td align="center" style="padding:0 40px;border:1px solid #e6e6e6;border-radius:2px" bgcolor="#FCFCFC" class="m_-5840917022289077987mlContentOuter">
						<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
							<tbody><tr>
								<td height="30" class="m_-5840917022289077987spacingHeight-40"></td>
							</tr>
							<tr>
								<td id="m_-5840917022289077987bodyText-8" style="font-family:'Open Sans',Arial,Helvetica,sans-serif;font-size:14px;line-height:150%;color:#6f6f6f">
									<table border="0" align="center" width="100%">
										<tbody style="font-family:'Open Sans',Arial,Helvetica,sans-serif;font-size:14px;line-height:150%;color:#6f6f6f">
										<tr>
											<td colspan="2"></td>
										</tr>
										<tr>
											<td colspan="2">
												<strong>Email: <?php echo esc_html( $search_results['email'] ) ?></strong>
												<br>
												<strong><?php echo $search_results['email']; ?></strong>
												<br><?php echo  esc_html( $search_results['address'] . ' (' . $search_results['radius'] . 'm radius)' ); ?>
												<br>
												<a href="<?php echo esc_attr( $search_results['edit_link'] ); ?>" style="font-family:'Open Sans',Arial,Helvetica,sans-serif;color:#409cff;text-decoration:underline">
                                                    View Search Submission
                                                </a>
											</td>
										</tr>
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>
										<tr>
											<td></td>
											<td style="width:70%">
												<?php
												$events = is_array($search_results['events']) ? $search_results['events'] : array();
												foreach ( $events as $event) {
													include(dirname(__FILE__)."/event_listing.php");
												}
												?>
											</td>
										</tr>
										<tr>
											<td colspan="2"></td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td height="30" class="m_-5840917022289077987spacingHeight-40"></td>
							</tr>
							</tbody></table>
					</td>
				</tr>
				</tbody></table>
		</td>
	</tr>
	</tbody>
</table>