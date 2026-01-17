<?php

namespace App\DataFixtures;

use App\Entity\Flow;
use App\Entity\FlowStep;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Shared step content (reused across flows)
        $physicalCheckContent =
            "Perform physical checks and reboot devices.\n\n" .
            "Ask the client to:\n" .
            "• Confirm that all fibre cable connections are properly connected and secure, and ask them to inspect for any loose, damaged, or improperly connected cables.\n" .
            "• Ensure the Ethernet cable between the ONT and router is properly connected.\n\n" .
            "Once confirmed:\n" .
            "• Power off the ONT for 30 seconds.\n" .
            "• Power the ONT back on.\n" .
            "• Then reboot the router.\n\n" .
            "After both devices are fully online:\n" .
            "• Recheck the ONT lights.\n" .
            "• Test connectivity again.";

        //
        // CREATE FLOW — Fibre: Slow Internet Access
        //

        $fibreSlowAccessFlow = new Flow();
        $fibreSlowAccessFlow->setTitle('Fibre — Slow Internet Access');
        $fibreSlowAccessFlow->setDescription('A guided troubleshooting flow for slow speeds on fibre connections.');
        $manager->persist($fibreSlowAccessFlow);


        //
        // STEP 1 — Check for outages
        //

        $step1 = new FlowStep();
        $step1->setFlow($fibreSlowAccessFlow);
        $step1->setStepNumber(1);
        $step1->setContent(
            "Check if the client is not affected by any outages.\n\n" .
            "• Check the Afrihost Network Status page or the FNO portal (where applicable) to confirm no open or ongoing outages exist in the client's area.\n"
        );
        $manager->persist($step1);

        //
        // STEP 2 — Physical checks + reboot (shared)
        //
        $step2 = new FlowStep();
        $step2->setFlow($fibreSlowAccessFlow);
        $step2->setStepNumber(2);
        $step2->setContent($physicalCheckContent);
        $manager->persist($step2);


        //
        // STEP 3 — Verify the correct speed profile (Mojo + FNO)
        //

        $step3 = new FlowStep();
        $step3->setFlow($fibreSlowAccessFlow);
        $step3->setStepNumber(3);
        $step3->setContent(
            "Verify the client's package and speed profile.\n\n" .
            "• Confirm the subscribed speed in Mojo.\n" .
            "• Check the FNO portal for the correct speed profile.\n" .
            "• Ensure there are no mismatches between the client's line speed and their provisioned profile."
        );
        $manager->persist($step3);


        //
        // STEP 4 — Check NIC speed negotiation + LAN cable
        //

        $step4 = new FlowStep();
        $step4->setFlow($fibreSlowAccessFlow);
        $step4->setStepNumber(4);
        $step4->setContent(
            "Verify the client's computer and LAN cable can support the subscribed speed.\n\n" .
            "• If a client is subscribed to a 100Mbps line or higher, confirm the PC's Ethernet NIC is 1Gbps capable.\n" .
            "• Ensure the client is using a CAT5e or CAT6 LAN cable.\n"
        );
        $manager->persist($step4);


        //
        // STEP 5 — Bypass router, test directly on ONT
        //

        $step5 = new FlowStep();
        $step5->setFlow($fibreSlowAccessFlow);
        $step5->setStepNumber(5);
        $step5->setContent(
            "Test the client's speeds while connected directly to the ONT.\n\n" .
            "• Have the client bypass the router & connect their PC directly to the ONT via LAN.\n" .
            "• Run a speed test.\n" .
            "• Compare the obtained speeds with the subscribed speeds.\n\n" .
            "If the speeds are correct directly on the ONT, this indicates that all is in order with the configured speeds on the fibre line — proceed to router troubleshooting."
        );
        $manager->persist($step5);


        //
        // STEP 6 — Test router via LAN, disable Wi-Fi, check bandwidth usage
        //

        $step6 = new FlowStep();
        $step6->setFlow($fibreSlowAccessFlow);
        $step6->setStepNumber(6);
        $step6->setContent(
            "Reconnect the router and perform a controlled LAN test.\n\n" .
            "• Disable the Wi-Fi network.\n" .
            "• Disconnect all other devices.\n" .
            "• Connect a single PC to the router via LAN.\n" .
            "• Ensure the client has no background downloads or apps using bandwidth.\n" .
            "• Run a speed test again."
        );
        $manager->persist($step6);


        //
        // STEP 7 — Improve Wi-Fi speeds (manual channel selection)
        //

        $step7 = new FlowStep();
        $step7->setFlow($fibreSlowAccessFlow);
        $step7->setStepNumber(7);
        $step7->setContent(
            "Improve wireless performance by adjusting Wi-Fi channel settings.\n\n" .
            "• Log into the router interface.\n" .
            "• Manually select a wireless channel.\n\n" .
            "Recommended non-overlapping channels:\n" .
            "• 2.4 GHz: Channels 1, 6, or 11\n" .
            "• 5 GHz: Channels 36, 40, 44, or 48\n\n" .
            "• Test connection stability and speed after changing channels."
        );
        $manager->persist($step7);


        //
        // STEP 8 — If slow at ONT: Gather evidence + log FNO fault
        //

        $step8 = new FlowStep();
        $step8->setFlow($fibreSlowAccessFlow);
        $step8->setStepNumber(8);
        $step8->setContent(
            "If speeds are slow even when connected directly to the ONT, this indicates a provider/FNO/line issue.\n\n" .
            "Collect the following before logging a fault:\n" .
            "• Speed test screenshot (full window, uncropped).\n" .
            "• Traceroute to a local server.\n" .
            "• Traceroute to an international server.\n\n" .
            "Note: Some FNOs have additional requirements (e.g., Frogfoot speed test results must be submitted together with a screenshot of the laptop's Task Manager, showing the CPU usage at the exact time the speed test was conducted).\n\n" .
            "Once collected, log an informative and detailed fault with the provider."
        );
        $manager->persist($step8);


        //
        // CREATE FLOW — Fibre: Intermittent Connection
        //

        $fibreIntermittentFlow = new Flow();
        $fibreIntermittentFlow->setTitle('Fibre — Intermittent Connection');
        $fibreIntermittentFlow->setDescription('A guided troubleshooting flow for intermittent fibre connectivity.');
        $manager->persist($fibreIntermittentFlow);

        //
        // STEP 1 — Check for outages
        //
        $iStep1 = new FlowStep();
        $iStep1->setFlow($fibreIntermittentFlow);
        $iStep1->setStepNumber(1);
        $iStep1->setContent(
            "Verify the client is not affected by any area outages.\n\n" .
            "• Check Afrihost Network Status and/or the relevant FNO portal for open incidents in the client's area."
        );
        $manager->persist($iStep1);

        //
        // STEP 2 — Physical checks + reboot (shared)
        //
        $iStep2 = new FlowStep();
        $iStep2->setFlow($fibreIntermittentFlow);
        $iStep2->setStepNumber(2);
        $iStep2->setContent($physicalCheckContent);
        $manager->persist($iStep2);

        //
        // STEP 3 — Clarify the intermittency pattern and scope
        //
        $iStep3 = new FlowStep();
        $iStep3->setFlow($fibreIntermittentFlow);
        $iStep3->setStepNumber(3);
        $iStep3->setContent(
            "Probe the client to understand the intermittency pattern.\n\n" .
            "Ask:\n" .
            "• Does the connection drop at specific times of day or randomly?\n" .
            "• Does it affect Wi-Fi, LAN, or both?\n" .
            "• Does it drop on all sites/services or only specific sites?"
        );
        $manager->persist($iStep3);

        //
        // STEP 4 — Run targeted tests based on the pattern
        //
        $iStep4 = new FlowStep();
        $iStep4->setFlow($fibreIntermittentFlow);
        $iStep4->setStepNumber(4);
        $iStep4->setContent(
            "Run targeted tests based on what you learned.\n\n" .
            "• If the drops happen at specific times, run ping tests during that window and record timestamps.\n" .
            "• If the issue occurs only on certain sites, consider changing the MTU size configured on the router.\n\n" .
            "Keep detailed notes and timestamps — this is useful evidence if a fault needs to be logged."
        );
        $manager->persist($iStep4);

        //
        // STEP 5 — Isolate router vs line (ONT vs router)
        //
        $iStep5 = new FlowStep();
        $iStep5->setFlow($fibreIntermittentFlow);
        $iStep5->setStepNumber(5);
        $iStep5->setContent(
            "Isolate whether the issue is on the router side or line/FNO side.\n\n" .
            "• Test the connection stability directly on the ONT (LAN) and then again via the router.\n" .
            "• Ask the client if there are any changes to the ONT lights during drops (e.g., LOS / optical alarms)."
        );
        $manager->persist($iStep5);

        //
        // STEP 6 — Continuous ping to default gateway (packet loss check)
        //
        $iStep6 = new FlowStep();
        $iStep6->setFlow($fibreIntermittentFlow);
        $iStep6->setStepNumber(6);
        $iStep6->setContent(
            "Check for packet loss to the default gateway.\n\n" .
            "1) Have the client run: ipconfig /all\n" .
            "2) Identify the Default Gateway IP.\n" .
            "3) Run a continuous ping: ping -t <gateway-ip>\n\n" .
            "• If there is packet loss to the gateway, the issue is likely on the FNO/line side.\n" .
            "• Where possible, check service history on the FNO portal for drops and prepare to log a detailed fault."
        );
        $manager->persist($iStep6);

        //
        // STEP 7 — Traceroute beyond gateway (identify where loss begins)
        //
        $iStep7 = new FlowStep();
        $iStep7->setFlow($fibreIntermittentFlow);
        $iStep7->setStepNumber(7);
        $iStep7->setContent(
            "If there is no packet loss to the gateway, test beyond the gateway.\n\n" .
            "• Run a traceroute to a specific site.\n" .
            "• Check if packet loss starts after the gateway.\n\n" .
            "If loss starts after the gateway:\n" .
            "• The issue may require escalation to Afrihost NOC.\n" .
            "• Include detailed notes of all troubleshooting done and full test results."
        );
        $manager->persist($iStep7);

        //
        // STEP 8 — Supporting tests during drops (local + international)
        //
        $iStep8 = new FlowStep();
        $iStep8->setFlow($fibreIntermittentFlow);
        $iStep8->setStepNumber(8);
        $iStep8->setContent(
            "Run supporting tests during the drops (especially if speed also deteriorates).\n\n" .
            "• Traceroute to a local server.\n" .
            "• Traceroute to an international server.\n" .
            "• Speed tests if performance degrades during the drops.\n\n" .
            "Keep full screenshots (uncropped) and record timestamps."
        );
        $manager->persist($iStep8);

        //
        // STEP 9 — If stable on ONT but unstable on Wi-Fi: router health checks
        //
        $iStep9 = new FlowStep();
        $iStep9->setFlow($fibreIntermittentFlow);
        $iStep9->setStepNumber(9);
        $iStep9->setContent(
            "If the connection is stable on the ONT but unstable on Wi-Fi, focus on router Wi-Fi health.\n\n" .
            "• Check if the router firmware is up to date; update if required.\n" .
            "• Manually select a Wi-Fi channel to reduce interference:\n" .
            "  - 2.4 GHz: Channels 1, 6, 11\n" .
            "  - 5 GHz: Channels 36, 40, 44, 48"
        );
        $manager->persist($iStep9);

        //
        // Save everything
        //
        $manager->flush();
    }
}



