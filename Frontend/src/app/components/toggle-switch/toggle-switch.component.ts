import { Component, Input } from "@angular/core";
import { FormsModule } from "@angular/forms";
import { ToggleType } from "../../../enums/toggle-type.enum";
import { AuthController } from "../../../controllers/auth.controller";
import { UserController } from "../../../controllers/user.controller";

@Component({
  selector: "app-toggle-switch",
  imports: [FormsModule],
  templateUrl: "./toggle-switch.component.html",
  styleUrl: "./toggle-switch.component.css",
})
export class ToggleSwitchComponent {
  @Input({ required: true })
  public value: boolean = false;

  @Input({ required: true })
  public type: ToggleType = ToggleType.FileHash;

  constructor(
    private readonly authController: AuthController,
    private readonly userController: UserController
  ) {}

  protected async toggleHash() {
    const user = this.authController.getUser();
    if (!user) {
      return;
    }

    user.settings.storageSettings.showFileHash = !this.value;
    await this.userController.changeSettings(user);
  }

  protected async toggleDate() {
    const user = this.authController.getUser();
    if (!user) {
      return;
    }

    user.settings.storageSettings.showUploadDate = !this.value;
    await this.userController.changeSettings(user);
  }

  protected readonly ToggleType = ToggleType;
}
