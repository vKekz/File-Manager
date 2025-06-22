import { Component } from "@angular/core";
import { AuthController } from "../../../controllers/auth.controller";
import {
  FileReplacementBehaviour,
  fileReplacementBehaviours,
  fileReplacementText,
} from "../../../enums/file-replacement-behaviour.enum";
import {
  storageSearchBehaviour,
  StorageSearchBehaviour,
  storageSearchText,
} from "../../../enums/storage-search-behaviour.enum";
import { ToggleSwitchComponent } from "../toggle-switch/toggle-switch.component";
import { UserController } from "../../../controllers/user.controller";
import { ToggleType } from "../../../enums/toggle-type.enum";
import { FormsModule } from "@angular/forms";

@Component({
  selector: "app-account-page",
  imports: [ToggleSwitchComponent, FormsModule],
  templateUrl: "./account-page.component.html",
  styleUrl: "./account-page.component.css",
})
export class AccountPageComponent {
  constructor(
    protected readonly authController: AuthController,
    private readonly userController: UserController
  ) {}

  protected async handleSearchBehaviourChange(event: Event) {
    const value = Number.parseInt((event.target as HTMLSelectElement).value);
    const user = this.authController.getUser();
    if (!user) {
      return;
    }

    user.settings.storageSettings.storageSearchBehaviour = value;
    await this.userController.changeSettings(user);
  }

  protected async handleFileReplaceBehaviourChange(event: Event) {
    const value = Number.parseInt((event.target as HTMLSelectElement).value);
    const user = this.authController.getUser();
    if (!user) {
      return;
    }

    user.settings.storageSettings.fileReplacementBehaviour = value;
    await this.userController.changeSettings(user);
  }

  protected readonly fileReplacementBehaviours = fileReplacementBehaviours;
  protected readonly FileReplacementBehaviour = FileReplacementBehaviour;
  protected readonly StorageSearchBehaviour = StorageSearchBehaviour;
  protected readonly storageSearchBehaviour = storageSearchBehaviour;
  protected readonly fileReplacementText = fileReplacementText;
  protected readonly storageSearchText = storageSearchText;
  protected readonly Boolean = Boolean;
  protected readonly ToggleType = ToggleType;
}
