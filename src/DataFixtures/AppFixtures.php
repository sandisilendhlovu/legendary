<?php

namespace App\DataFixtures;

use App\Entity\FlowStepOption;
use App\Entity\Flow;
use App\Entity\FlowStep;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $fibreProduct = $manager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'Fibre']);

        if (!$fibreProduct) {
            throw new \RuntimeException(
                'Product "Fibre" not found. Please ensure the Fibre product exists before loading fixtures.'
            );
        }

        $now = new \DateTimeImmutable();

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

        // define title variable + check if Slow Access flow already exists
        $fibreSlowAccessTitle = 'Fibre — Slow Internet Access';
        $fibreSlowAccessFlow = $manager
            ->getRepository(Flow::class)
            ->findOneBy(['title' => $fibreSlowAccessTitle]);

        // only create the flow + steps if it does NOT already exist
        if (!$fibreSlowAccessFlow) {


            //
            // CREATE FLOW — Fibre: Slow Internet Access
            //

            $fibreSlowAccessFlow = new Flow();
            $fibreSlowAccessFlow->setTitle($fibreSlowAccessTitle);
            $fibreSlowAccessFlow->setDescription('A guided troubleshooting flow for slow speeds on fibre connections.');

            $fibreSlowAccessFlow->setProduct($fibreProduct);

            $manager->persist($fibreSlowAccessFlow);


            //
            // STEP 1 — Check for outages
            //

            $step1 = new FlowStep();
            $step1->setFlow($fibreSlowAccessFlow);
            $step1->setStepNumber(1);

            $step1->setTitle('Check for outages');

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

            $step2->setTitle('Physical checks + reboot');


            $step2->setContent($physicalCheckContent);
            $manager->persist($step2);


            //
            // STEP 3 — Verify the correct speed profile (Mojo + FNO)
            //

            $step3 = new FlowStep();
            $step3->setFlow($fibreSlowAccessFlow);
            $step3->setStepNumber(3);

            $step3->setTitle('Verify speed profile');

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

            $step4->setTitle('Check LAN hardware capability');

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

            $step5->setTitle('Test directly on ONT');

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

            $step6->setTitle('Controlled router test');

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

            $step7->setTitle('Improve Wi-Fi performance');

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

            $step8->setTitle('Escalation evidence (if slow on ONT)');

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

            $manager->flush();

            // Fibre Slow Access decision tree
            // STEP 1 — Outage?

            $opt1a = new FlowStepOption();
            $opt1a->setLabel('Outage found in area');
            $opt1a->setCurrentStep($step1);
            $opt1a->setNextStep(null);
            $manager->persist($opt1a);

            $opt1b = new FlowStepOption();
            $opt1b->setLabel('No outage / clear');
            $opt1b->setCurrentStep($step1);
            $opt1b->setNextStep($step2);
            $manager->persist($opt1b);

        // STEP 2
            $opt2a = new FlowStepOption();
            $opt2a->setLabel('Issue resolved / speeds improved');
            $opt2a->setCurrentStep($step2);
            $opt2a->setNextStep(null);
            $manager->persist($opt2a);

            $opt2b = new FlowStepOption();
            $opt2b->setLabel('Still slow');
            $opt2b->setCurrentStep($step2);
            $opt2b->setNextStep($step3);
            $manager->persist($opt2b);

        // STEP 3 — Speed profile correct?
            $opt3a = new FlowStepOption();
            $opt3a->setLabel('Speed profile correct');
            $opt3a->setCurrentStep($step3);
            $opt3a->setNextStep($step4);
            $manager->persist($opt3a);

            $opt3b = new FlowStepOption();
            $opt3b->setLabel('Speed profile mismatch (fix / escalate)');
            $opt3b->setCurrentStep($step3);
            $opt3b->setNextStep(null);
            $manager->persist($opt3b);

        // STEP 4 — LAN hardware ok?
            $opt4a = new FlowStepOption();
            $opt4a->setLabel('LAN hardware ok');
            $opt4a->setCurrentStep($step4);
            $opt4a->setNextStep($step5);
            $manager->persist($opt4a);

            $opt4b = new FlowStepOption();
            $opt4b->setLabel('LAN limitation found (fix cable/NIC)');
            $opt4b->setCurrentStep($step4);
            $opt4b->setNextStep(null);
            $manager->persist($opt4b);

        // STEP 5
            $opt5a = new FlowStepOption();
            $opt5a->setLabel('Speeds correct on ONT');
            $opt5a->setCurrentStep($step5);
            $opt5a->setNextStep($step6);
            $manager->persist($opt5a);

            $opt5b = new FlowStepOption();
            $opt5b->setLabel('Still slow on ONT');
            $opt5b->setCurrentStep($step5);
            $opt5b->setNextStep($step8);
            $manager->persist($opt5b);

        // STEP 6 — Router controlled test result
            $opt6a = new FlowStepOption();
            $opt6a->setLabel('Speeds now correct');
            $opt6a->setCurrentStep($step6);
            $opt6a->setNextStep(null);
            $manager->persist($opt6a);

            $opt6b = new FlowStepOption();
            $opt6b->setLabel('Still slow via router');
            $opt6b->setCurrentStep($step6);
            $opt6b->setNextStep($step7);
            $manager->persist($opt6b);

        // STEP 7 — Wi-Fi improvement result
            $opt7a = new FlowStepOption();
            $opt7a->setLabel('Wi-Fi improved / resolved');
            $opt7a->setCurrentStep($step7);
            $opt7a->setNextStep(null);
            $manager->persist($opt7a);

            $opt7b = new FlowStepOption();
            $opt7b->setLabel('Still slow');
            $opt7b->setCurrentStep($step7);
            $opt7b->setNextStep($step8);
            $manager->persist($opt7b);

        // STEP 8 — Escalation done
            $opt8a = new FlowStepOption();
            $opt8a->setLabel('Evidence captured — log fault / escalate');
            $opt8a->setCurrentStep($step8);
            $opt8a->setNextStep(null);
            $manager->persist($opt8a);

            $manager->flush();

        }


        // define title variable + check if Intermittent flow already exists
        $fibreIntermittentTitle = 'Fibre — Intermittent Connection';
        $fibreIntermittentFlow = $manager
            ->getRepository(Flow::class)
            ->findOneBy(['title' => $fibreIntermittentTitle]);

        // only create the flow + steps if it does NOT already exist
        if (!$fibreIntermittentFlow) {


            //
            // CREATE FLOW — Fibre: Intermittent Connection
            //

            $fibreIntermittentFlow = new Flow();
            $fibreIntermittentFlow->setTitle($fibreIntermittentTitle);
            $fibreIntermittentFlow->setDescription('A guided troubleshooting flow for intermittent fibre connectivity.');
            $fibreIntermittentFlow->setProduct($fibreProduct);

            $manager->persist($fibreIntermittentFlow);

            //
            // STEP 1 — Check for outages
            //
            $iStep1 = new FlowStep();
            $iStep1->setFlow($fibreIntermittentFlow);
            $iStep1->setStepNumber(1);
            $iStep1->setTitle('Check for outages');
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
            $iStep2->setTitle('Physical checks + reboot');
            $iStep2->setContent($physicalCheckContent);
            $manager->persist($iStep2);

            //
            // STEP 3 — Clarify the intermittency pattern and scope
            //
            $iStep3 = new FlowStep();
            $iStep3->setFlow($fibreIntermittentFlow);
            $iStep3->setStepNumber(3);
            $iStep3->setTitle('Identify the intermittency pattern');
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
            $iStep4->setTitle('Run targeted tests');
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
            $iStep5->setTitle('Isolate router vs line');
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
            $iStep6->setTitle('Packet loss check (gateway ping)');
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
            $iStep7->setTitle('Traceroute to locate loss');
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
            $iStep8->setTitle('Supporting evidence during drops');
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
            $iStep9->setTitle('Router Wi-Fi health checks');
            $iStep9->setContent(
                "If the connection is stable on the ONT but unstable on Wi-Fi, focus on router Wi-Fi health.\n\n" .
                "• Check if the router firmware is up to date; update if required.\n" .
                "• Manually select a Wi-Fi channel to reduce interference:\n" .
                "  - 2.4 GHz: Channels 1, 6, 11\n" .
                "  - 5 GHz: Channels 36, 40, 44, 48"
            );
            $manager->persist($iStep9);

            $manager->flush();


            // Fibre Intermittent decision tree

// ==========================
// STEP 1 OPTIONS — Outage?
// ==========================
            $iOpt1a = new FlowStepOption();
            $iOpt1a->setLabel('Outage found in area');
            $iOpt1a->setCurrentStep($iStep1);
            $iOpt1a->setNextStep(null);
            $manager->persist($iOpt1a);

            $iOpt1b = new FlowStepOption();
            $iOpt1b->setLabel('No outage / clear');
            $iOpt1b->setCurrentStep($iStep1);
            $iOpt1b->setNextStep($iStep2);
            $manager->persist($iOpt1b);

// ==================================
// STEP 2 OPTIONS — Resolved reboot?
// ==================================
            $iOpt2a = new FlowStepOption();
            $iOpt2a->setLabel('Resolved after reboot/checks');
            $iOpt2a->setCurrentStep($iStep2);
            $iOpt2a->setNextStep(null);
            $manager->persist($iOpt2a);

            $iOpt2b = new FlowStepOption();
            $iOpt2b->setLabel('Still intermittent');
            $iOpt2b->setCurrentStep($iStep2);
            $iOpt2b->setNextStep($iStep3);
            $manager->persist($iOpt2b);

// =====================================
// STEP 3 OPTIONS — Continue to testing
// =====================================
            $iOpt3a = new FlowStepOption();
            $iOpt3a->setLabel('Continue to targeted tests');
            $iOpt3a->setCurrentStep($iStep3);
            $iOpt3a->setNextStep($iStep4);
            $manager->persist($iOpt3a);

// ===============================
// STEP 4 OPTIONS — Continue
// ===============================
            $iOpt4a = new FlowStepOption();
            $iOpt4a->setLabel('Continue to isolation test');
            $iOpt4a->setCurrentStep($iStep4);
            $iOpt4a->setNextStep($iStep5);
            $manager->persist($iOpt4a);

// =======================================
// STEP 5 OPTIONS — Router vs line (yours)
// =======================================
            $iOpt5a = new FlowStepOption();
            $iOpt5a->setLabel('Drops happen on ONT (LAN) too');
            $iOpt5a->setCurrentStep($iStep5);
            $iOpt5a->setNextStep($iStep6);
            $manager->persist($iOpt5a);

            $iOpt5b = new FlowStepOption();
            $iOpt5b->setLabel('Stable on ONT, only drops via router/Wi-Fi');
            $iOpt5b->setCurrentStep($iStep5);
            $iOpt5b->setNextStep($iStep9);
            $manager->persist($iOpt5b);

// =====================================
// STEP 6 OPTIONS — Packet loss (yours)
// =====================================
            $iOpt6a = new FlowStepOption();
            $iOpt6a->setLabel('Packet loss to gateway');
            $iOpt6a->setCurrentStep($iStep6);
            $iOpt6a->setNextStep($iStep8);
            $manager->persist($iOpt6a);

            $iOpt6b = new FlowStepOption();
            $iOpt6b->setLabel('No packet loss to gateway');
            $iOpt6b->setCurrentStep($iStep6);
            $iOpt6b->setNextStep($iStep7);
            $manager->persist($iOpt6b);

// =======================================
// FIX #2: STEP 7 OPTIONS — MUST go to 8
// =======================================
            $iOpt7a = new FlowStepOption();
            $iOpt7a->setLabel('Loss/timeouts start after gateway');
            $iOpt7a->setCurrentStep($iStep7);
            $iOpt7a->setNextStep($iStep8);
            $manager->persist($iOpt7a);

            $iOpt7b = new FlowStepOption();
            $iOpt7b->setLabel('Traceroute looks normal (no loss)');
            $iOpt7b->setCurrentStep($iStep7);
            $iOpt7b->setNextStep($iStep8);
            $manager->persist($iOpt7b);

// =====================================
// STEP 8 OPTIONS — Evidence captured?
// =====================================
            $iOpt8a = new FlowStepOption();
            $iOpt8a->setLabel('Evidence captured — log fault / escalate');
            $iOpt8a->setCurrentStep($iStep8);
            $iOpt8a->setNextStep(null);
            $manager->persist($iOpt8a);

// ====================================
// STEP 9 OPTIONS — Wi-Fi fixed or not
// ====================================
            $iOpt9a = new FlowStepOption();
            $iOpt9a->setLabel('Resolved after Wi-Fi/router changes');
            $iOpt9a->setCurrentStep($iStep9);
            $iOpt9a->setNextStep(null);
            $manager->persist($iOpt9a);

            $iOpt9b = new FlowStepOption();
            $iOpt9b->setLabel('Still dropping / not resolved');
            $iOpt9b->setCurrentStep($iStep9);
            $iOpt9b->setNextStep($iStep8); //
            $manager->persist($iOpt9b);


            $manager->flush();

        }

    }

}


