# FIT - Go Live / activation checklist

This checklist tracks production dependencies that must not be simulated in code.

## Biometric integrity

- [ ] Verify Render secret variables from an interactive SSH session on the live service before enabling biometric providers.
- [ ] Confirm `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` and `AWS_DEFAULT_REGION` are present in Render and scoped to the minimum required AWS permissions.
- [ ] Confirm AWS IAM allows `rekognition:CompareFaces` only for the FIT runtime identity where practical.
- [ ] Validate `AWS_REKOGNITION_SIMILARITY_THRESHOLD` with federation policy before operational use.
- [ ] Obtain the licensed signotec Biometrics API SDK/documentation and licence from signotec.
- [ ] Deploy the internal FIT signotec bridge and configure `SIGNOTEC_BRIDGE_URL`, `SIGNOTEC_BRIDGE_TOKEN` and `SIGNOTEC_LICENSE_ID` in Render secrets.
- [ ] Validate dynamic signature capture on approved signotec hardware before enabling automated signature comparison.
- [ ] Confirm retention, access control and audit policy for biometric references/results with the federation/DPO.

## Existing pre-Go-Live items

- [ ] Realign obsolete legacy tests with the canonical FIT workflows without weakening current security.
- [ ] Inventory FIT working copies/worktrees and remove only confirmed unused copies.
- [ ] Provision the licensed FIFA XSD package outside Git and validate `FIFA_CONNECT_XSD_PATH` in CI/Render.
- [ ] Run final FIFA validation in an authorised environment and archive the result.
